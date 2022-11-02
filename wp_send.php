<?php
/**
 * Plugin Name:       wp_mail replacement
 * Plugin URI:        https://wordpress.com
 * Description:       wp_mail replacement for GMail SMTP
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Praveen Dias
 * Author URI:        https://wordpress.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://wordpress.com
 * Text Domain:       wp_mail
 * Domain Path:       /languages
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


require_once('vendor/autoload.php');
require_once('mail-config.php');

add_filter('pre_wp_mail', 'wp_mail_replacement', 10, 2);

function wp_mail_replacement($null, $atts){
    wp_mail_smtp_replacement($atts['to'], $atts['subject'], $atts['message']);
    return true;
}

function wp_mail_smtp_replacement($to, $subject, $body){

    $mail = new PHPMailer(true);

    try {
        //Server settings
        //$mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->isSMTP();
        $mail->Host       = P1180_SMTP;
        $mail->SMTPAuth   = true;
        $mail->Username   = P1180_MAIL_FROM;
        $mail->Password   = P1180_APP_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465; //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
    
        //Recipients
        $mail->setFrom(P1180_MAIL_FROM);
        $mail->addAddress($to);
        $mail->addReplyTo(P1180_MAIL_FROM, 'P1180');    
    
        //Content
        $mail->isHTML(true); 
        $mail->Subject = $subject;
        $mail->Body    = $body;
    
        $mail->send();
    } catch (Exception $e) {
        $message = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        throw new Exception($message);
    }
}
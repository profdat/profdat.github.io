<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'include/phpmailer/src/Exception.php';
require 'include/phpmailer/src/PHPMailer.php';
require 'include/phpmailer/src/SMTP.php';

enviarEmail()

function enviarEmail(){
    if (isset($_POST['template-contactform-name']) && isset($_POST['template-contactform-email']) && isset($_POST['template-contactform-phone']) && isset($_POST['subject']) && isset($_POST['template-contactform-message'])){

        //mandar correo
        $name = $_POST['template-contactform-name'];
        $email = $_POST['template-contactform-email'];
        $phone = $_POST['template-contactform-phone'];
        $subject = $_POST['subject'];
        $message = $_POST['template-contactform-message'];

        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->SMTPDebug = 2;                                       // Enable verbose debug output
            $mail->isSMTP();                                            // Send using SMTP
            $mail->Host       = 'smtp.gmail.com';                       // Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
            $mail->Username   = 'athendat.cu@gmail.com';                // SMTP username
            $mail->Password   = 'Palas_Atenea05.20';                    // SMTP password
            $mail->SMTPSecure = 'tls';                                  // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
            $mail->Port       = 587;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

            //Recipients
            $mail->setFrom('athendat.cu@gmail.com', 'ATHENDAT');
            $mail->addAddress('fr20587@gmail.com', 'ATHENDAT');     // Add a recipient
            //$mail->addAddress('ellen@example.com');               // Name is optional
            //$mail->addReplyTo('info@example.com', 'Information');
            //$mail->addCC('cc@example.com');
            //$mail->addBCC('bcc@example.com');

            // Attachments
            //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
            //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

            // Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = 'Correo de Contacto';
            $mail->Body    = 'Nombre:' .$name. '<br/> Correo: ' .$email 'This is the HTML message body <b>in bold!</b>';
            //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $mail->send();
            echo 'Message has been sent';
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }

    }else{
        return;
    }
}

?>

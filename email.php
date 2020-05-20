<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/phpmailer/Exception.php';
require 'phpmailer/phpmailer/PHPMailer.php';
require 'phpmailer/phpmailer/SMTP.php';

require 'vendor/autoload.php';

enviarEmail();

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
            $mail->SMTPDebug = 2;
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'athendat.cu@gmail.com';
            $mail->Password = 'Palas_Atenea05.20';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            //Recipients
            $mail->setFrom('athendat.cu@gmail.com', 'ATHENDAT');
            $mail->addAddress('fr20587@gmail.com', 'ATHENDAT');


            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Correo de Contacto';
            $mail->Body = 'Nombre: ' . $name . '<br/>Correo: ' . $email . '<br/>Teléfono:' . $phone . '<br/>Asunto:' . $subject . '<br/>Mensaje:' . $message;


            $mail->send();
            echo 'El mensaje ha sido enviado.';
        } catch (Exception $e) {
            echo 'El mensaje no se ha podido enviar. Mailer Error:', $mail->ErrorInfo;
        }

    }else{
        return;
    }
}



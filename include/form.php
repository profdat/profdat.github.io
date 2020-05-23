<?php

/*-------------------------------------------------

	Form Processor Plugin
	by SemiColonWeb

---------------------------------------------------*/


/*-------------------------------------------------
	PHPMailer Initialization Files
---------------------------------------------------*/

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';


/*-------------------------------------------------
	Receiver's Email
---------------------------------------------------*/

$toemails = array();

$toemails[] = array(
				'email' => 'FR20587@gmail.com', // Your Email Address
				'name' => 'Frank Rodríguez López' // Your Name
			);


/*-------------------------------------------------
	Sender's Email
---------------------------------------------------*/

$fromemail = array(
				'email' => 'athenda.cu@gmail.com', // Company's Email Address (preferably currently used Domain Name)
				'name' => 'ATHENDAT' // Company Name
			);


/*-------------------------------------------------
	reCaptcha
---------------------------------------------------*/

// Add this only if you use reCaptcha with your Contact Forms
$recaptcha_secret = ''; // Your reCaptcha Secret


/*-------------------------------------------------
	PHPMailer Initialization
---------------------------------------------------*/

$mail = new PHPMailer( exception: true);

/* Add your SMTP Codes after this Line */

     $mail -> isSMTP();

     $mail -> Host = 'smpt.gmail.com';
     $mail -> Port = 587;

     $mail -> SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;;

     $mail -> SMTPAuth = true;

     $mail -> Username = 'athendat.cu@gmail.com';
     $mail -> Password = 'Palas_Atenea05.20';

     $mail->setFrom('athendat.cu@gmail.com', 'Equipo de ATHENDAT');

     $mail->addReplyTo('replyto@example.com', 'First Last')

     //Set who the message is to be sent to
     $mail->addAddress('whoto@example.com', 'John Doe');

     //Set the subject line
     $mail->Subject = 'PHPMailer GMail SMTP test'





catch (Exception $exception) {
        echo 'Algo salio mal', $exception -> getMessage();
    }

// End of SMTP


/*-------------------------------------------------
	Form Messages
---------------------------------------------------*/

$message = array(
	'success'			=> 'Hemos recibido su Mensaje <strong>sactifactoriamente</strong> le enviaremos una respuesta lo antes posible.',
	'error'				=> '<strong>Correo no enviado</strong> debido a un error inesperado. Por Favor Inténtelo Nuevamente.',
	'error_bot'			=> 'Bot Detectado! Formulario no procesado! Por Favor Inténtelo Nuevamente!',
	'error_unexpected'	=> '<strong>Error Inesperado</strong>. Por Favor Inténtelo Más Tarde.',
	'recaptcha_invalid'	=> 'Captcha no Valido! Por Favor Inténtelo Nuevamente!',
	'recaptcha_error'	=> 'Captcha not Envidado! Por Favor Inténtelo Nuevamente.'
);


/*-------------------------------------------------
	Form Processor
---------------------------------------------------*/

if( $_SERVER['REQUEST_METHOD'] == 'POST' ) {

	$prefix		= !empty( $_POST['prefix'] ) ? $_POST['prefix'] : '';
	$submits	= $_POST;
	$botpassed	= false;


	$message_form					= !empty( $submits['message'] ) ? $submits['message'] : array();
	$message['success']				= !empty( $message_form['success'] ) ? $message_form['success'] : $message['success'];
	$message['error']				= !empty( $message_form['error'] ) ? $message_form['error'] : $message['error'];
	$message['error_bot']			= !empty( $message_form['error_bot'] ) ? $message_form['error_bot'] : $message['error_bot'];
	$message['error_unexpected']	= !empty( $message_form['error_unexpected'] ) ? $message_form['error_unexpected'] : $message['error_unexpected'];
	$message['recaptcha_invalid']	= !empty( $message_form['recaptcha_invalid'] ) ? $message_form['recaptcha_invalid'] : $message['recaptcha_invalid'];
	$message['recaptcha_error']		= !empty( $message_form['recaptcha_error'] ) ? $message_form['recaptcha_error'] : $message['recaptcha_error'];


	/*-------------------------------------------------
		Bot Protection
	---------------------------------------------------*/

	if( isset( $submits[ $prefix . 'botcheck' ] ) ) {
		$botpassed = true;
	}

	if( !empty( $submits[ $prefix . 'botcheck' ] ) ) {
		$botpassed = false;
	}

	if( $botpassed == false ) {
		echo '{ "alert": "error", "message": "' . $message['error_bot'] . '" }';
		exit;
	}


	/*-------------------------------------------------
		reCaptcha
	---------------------------------------------------*/

	if( isset( $submits['g-recaptcha-response'] ) ) {

		$recaptcha_data = array(
			'secret' => $recaptcha_secret,
			'response' => $submits['g-recaptcha-response']
		);

		$recap_verify = curl_init();
		curl_setopt( $recap_verify, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify" );
		curl_setopt( $recap_verify, CURLOPT_POST, true );
		curl_setopt( $recap_verify, CURLOPT_POSTFIELDS, http_build_query( $recaptcha_data ) );
		curl_setopt( $recap_verify, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $recap_verify, CURLOPT_RETURNTRANSFER, true );
		$recap_response = curl_exec( $recap_verify );

		$g_response = json_decode( $recap_response );

		if ( $g_response->success !== true ) {
			echo '{ "alert": "error", "message": "' . $message['recaptcha_invalid'] . '" }';
			exit;
		}
	}

	$template	= !empty( $submits['template'] ) ? $submits['template'] : 'html';
	$html_title	= !empty( $submits['html_title'] ) ? $submits['html_title'] : 'Form Response';
	$forcerecap	= ( !empty( $submits['force_recaptcha'] ) && $submits['force_recaptcha'] != 'false' ) ? true : false;
	$replyto	= !empty( $submits['replyto'] ) ? explode( ',', $submits['replyto'] ) : false;

	if( $forcerecap ) {
		if( !isset( $submits['g-recaptcha-response'] ) ) {
			echo '{ "alert": "error", "message": "' . $message['recaptcha_error'] . '" }';
			exit;
		}
	}

	/*-------------------------------------------------
		Auto-Responders
	---------------------------------------------------*/

	$autores	= ( !empty( $submits['autoresponder'] ) && $submits['autoresponder'] != 'false' ) ? true : false;
	$ar_subject	= !empty( $submits['ar_subject'] ) ? $submits['ar_subject'] : 'Gracias por su Correo';
	$ar_title	= !empty( $submits['ar_title'] ) ? $submits['ar_title'] : 'Para Nosotros es un Placer Recibir su Mensaje!';
	$ar_message	= !empty( $submits['ar_message'] ) ? $submits['ar_message'] : 'Auto Respuesta';

	preg_match_all('#\{(.*?)\}#', $ar_message, $ar_matches);
	if( !empty( $ar_matches[1] ) ) {
		foreach( $ar_matches[1] as $ar_key => $ar_value ) {
			$ar_message = str_replace( '{' . $ar_value . '}' , $submits[ $ar_value ], $ar_message );
		}
	}

	$ar_footer	= !empty( $submits['ar_footer'] ) ? $submits['ar_footer'] : 'Derechos de Autor &copy; ' . date('Y') . ' <strong>SemiColonWeb</strong>. Todos los Derechos Reservados por PROFDAT.';

	$mail->Subject = !empty( $submits['subject'] ) ? $submits['subject'] : 'Form Response from your Website';
	$mail->SetFrom( $fromemail['email'] , $fromemail['name'] );

	if( !empty( $replyto ) ) {
		if( count( $replyto ) > 1 ) {
			$replyto_e = $submits[ $replyto[0] ];
			$replyto_n = $submits[ $replyto[1] ];
			$mail->AddReplyTo( $replyto_e , $replyto_n );
		} elseif( count( $replyto ) == 1 ) {
			$replyto_e = $submits[ $replyto[0] ];
			$mail->AddReplyTo( $replyto_e );
		}
	}

	foreach( $toemails as $toemail ) {
		$mail->AddAddress( $toemail['email'] , $toemail['name'] );
	}

	$unsets = array( 'prefix', 'subject', 'replyto', 'template', 'html_title', 'message', 'autoresponder', 'ar_subject', 'ar_title', 'ar_message', 'ar_footer', $prefix . 'botcheck', 'g-recaptcha-response', 'force_recaptcha', $prefix . 'submit' );

	foreach( $unsets as $unset ) {
		unset( $submits[ $unset ] );
	}

	$fields = array();

	foreach( $submits as $name => $value ) {

		if( empty( $value ) ) continue;

		$name = str_replace( $prefix , '', $name );
		$name = ucwords( str_replace( '-', ' ', $name ) );

		if( is_array( $value ) ) {
			$value = implode( ', ', $value );
		}

		$fields[$name] = $value;

	}

	$files = $_FILES;

	foreach( $files as $file => $filevalue ) {

		if( is_array( $filevalue['name'] ) ) {

			$filecount = count( $filevalue['name'] );

			for( $f = 0; $f < $filecount; $f++ ) {
				if ( isset( $_FILES[ $file ] ) && $_FILES[ $file ]['error'][ $f ] == UPLOAD_ERR_OK ) {
					$mail->IsHTML(true);
					$mail->AddAttachment( $_FILES[ $file ]['tmp_name'][ $f ], $_FILES[ $file ]['name'][ $f ] );
				}
			}

		} else {

			if ( isset( $_FILES[ $file ] ) && $_FILES[ $file ]['error'] == UPLOAD_ERR_OK ) {
				$mail->IsHTML(true);
				$mail->AddAttachment( $_FILES[ $file ]['tmp_name'], $_FILES[ $file ]['name'] );
			}

		}

	}

	$response = array();

	foreach( $fields as $fieldname => $fieldvalue ) {
		if( $template == 'text' ) {
			$response[] = $fieldname . ': ' . $fieldvalue;
		} else {
			$fieldname = '<tr>
								<td class="hero-subheader__title" style="font-size: 16px; line-height: 24px; font-weight: bold; padding: 0 0 5px 0;" align="left">' . $fieldname . '</td>
							</tr>';
			$fieldvalue = '<tr>
								<td class="hero-subheader__content" style="font-size: 16px; line-height: 24px; color: #777777; padding: 0 15px 30px 0;" align="left">' . $fieldvalue . '</td>
							</tr>';
			$response[] = $fieldname . $fieldvalue;
		}
	}

	$referrer = $_SERVER['HTTP_REFERER'] ? '<br><br><br>This Form was submitted from: ' . $_SERVER['HTTP_REFERER'] : '';

	$html_before = '<table class="full-width-container" border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" bgcolor="#eeeeee" style="width: 100%; height: 100%; padding: 50px 0 50px 0;">
				<tr>
					<td align="center" valign="top">
						<!-- / 700px container -->
						<table class="container" border="0" cellpadding="0" cellspacing="0" width="84%" bgcolor="#ffffff" style="width: 84%;">
							<tr>
								<td align="center" valign="top">
									';

	$html_after = '<!-- /// Footer -->
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>';

	if( $template == 'text' ) {
		$body = implode( "<br>", $response ) . $referrer;
	} else {
		$html = $html_before . '<!-- / Header -->
									<table class="container header" border="0" cellpadding="0" cellspacing="0" width="84%" style="width: 84%;">
										<tr>
											<td style="padding: 30px 0 30px 0; border-bottom: solid 1px #eeeeee; font-size: 30px; font-weight: bold; text-decoration: none; color: #000000;" align="left">
												' . $html_title . '
											</td>
										</tr>
									</table>
									<!-- /// Header -->

									<!-- / Hero subheader -->
									<table class="container hero-subheader" border="0" cellpadding="0" cellspacing="0" width="84%" style="width: 84%; padding: 60px 0 30px 0;"">
										' . implode( '', $response ) . '
									</table>

									<!-- / Footer -->
									<table class="container" border="0" cellpadding="0" cellspacing="0" width="100%" align="center">
										<tr>
											<td align="center">
												<table class="container" border="0" cellpadding="0" cellspacing="0" width="84%" align="center" style="border-top: 1px solid #eeeeee; width: 84%;">
													<tr>
														<td style="color: #d5d5d5; text-align: center; font-size: 12px; padding: 30px 0 30px 0; line-height: 22px;">' . strip_tags( $referrer ) . '</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
									' . $html_after;

		$body = $html;
	}

	if( $autores && !empty( $replyto_e ) ) {
		$autoresponder = new PHPMailer();

		/* Add your Auto-Responder SMTP Codes after this Line */


		// End of Auto-Responder SMTP

		$autoresponder->SetFrom( $fromemail['email'] , $fromemail['name'] );
		if( !empty( $replyto_n ) ) {
			$autoresponder->AddAddress( $replyto_e , $replyto_n );
		} else {
			$autoresponder->AddAddress( $replyto_e );
		}
		$autoresponder->Subject = $ar_subject;

		$ar_body = $html_before . '<!-- / Header -->
					<table class="container header" border="0" cellpadding="0" cellspacing="0" width="84%" style="width: 84%;">
						<tr>
							<td style="padding: 30px 0 30px 0; border-bottom: solid 1px #eeeeee; font-size: 30px; font-weight: bold; text-decoration: none; color: #000000;" align="left">
								' . $ar_title . '
							</td>
						</tr>
					</table>
					<!-- /// Header -->

					<!-- / Hero subheader -->
					<table class="container hero-subheader" border="0" cellpadding="0" cellspacing="0" width="84%" style="width: 84%; padding: 60px 0 30px 0;"">
						<tr>
							<td class="hero-subheader__content" style="font-size: 16px; line-height: 26px; color: #777777; padding: 0 15px 30px 0;" align="left">' . $ar_message . '</td>
						</tr>
					</table>

					<!-- / Footer -->
					<table class="container" border="0" cellpadding="0" cellspacing="0" width="100%" align="center">
						<tr>
							<td align="center">
								<table class="container" border="0" cellpadding="0" cellspacing="0" width="84%" align="center" style="border-top: 1px solid #eeeeee; width: 84%;">
									<tr>
										<td style="color: #d5d5d5; text-align: center; font-size: 12px; padding: 30px 0 30px 0; line-height: 22px;">' . $ar_footer . '</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
					' . $html_after;

		$autoresponder->MsgHTML( $ar_body );
	}

	$mail->MsgHTML( $body );
	$sendEmail = $mail->Send();

	if( $sendEmail == true ):

		if( $autores && !empty( $replyto_e ) ) {
			$send_arEmail = $autoresponder->Send();
		}

		echo '{ "alert": "success", "message": "' . $message['success'] . '" }';
	else:
		echo '{ "alert": "error", "message": "' . $message['error'] . '<br><br><strong>Reason:</strong><br>' . $mail->ErrorInfo . '" }';
	endif;

} else {
	echo '{ "alert": "error", "message": "' . $message['error_unexpected'] . '" }';
}

?>
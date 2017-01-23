<?php

// ========================================================================================
//
// EMAIL.API
//
// # Author......................: Andrei Busuioc (Devman)
// # Author......................: Tobias Strunz (Fhizban)
// # Sourceforge Download........: https://sourceforge.net/projects/d13/
// # Github Repo.................: https://github.com/CriticalHit-d13/d13
// # Project Documentation.......: http://www.critical-hit.biz
// # License.....................: https://creativecommons.org/licenses/by/4.0/
//
//
// ========================================================================================
// ----------------------------------------------------------------------------------------
// email
// ----------------------------------------------------------------------------------------

function email($fromEmail, $fromName, $to, $subject, $body)
{
	require (CONST_INCLUDE_PATH . 'plugins/phpmailer/PHPMailerAutoload.php');

	$results_messages = array();
	$mail = new PHPMailer(true);
	$mail->CharSet = 'utf-8';
	ini_set('default_charset', 'UTF-8');
	class phpmailerAppException extends phpmailerException

	{
	}

	try {
		if (!PHPMailer::validateAddress($to)) {
			throw new phpmailerAppException("Email address " . $to . " is invalid -- aborting!");
		}

		$mail->isSMTP();
		$mail->SMTPDebug = 0;
		$mail->Host = "localhost";
		$mail->Port = "25";
		$mail->SMTPSecure = "none";
		$mail->SMTPAuth = false;
		$mail->addReplyTo($fromEmail, $fromName);
		$mail->setFrom($fromEmail, $fromName);
		$mail->addAddress($to);
		$mail->Subject = $subject;
		$mail->WordWrap = 78;
		$mail->msgHTML($body, dirname(__FILE__) , true);
		try {
			$mail->send();
		}

		catch(phpmailerException $e) {
			throw new phpmailerAppException('Unable to send to: ' . $to . ': ' . $e->getMessage());
		}
	}

	catch(phpmailerAppException $e) {
		$results_messages[] = $e->errorMessage();
	}

	if ($mail->IsError()) {
		return 'emailNotSent';
	}
	else {
		return 'emailSent';
	}
}

// =====================================================================================EOF

?>
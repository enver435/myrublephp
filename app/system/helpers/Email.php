<?php

	namespace App\System\Helpers;

	class Email
	{
		/**
		 * Validate Email
		 *
		 * @param string $email
		 * @return boolean
		 */
		public static function valid($email)
		{
			$email = filter_var($email, FILTER_SANITIZE_EMAIL);
			if (!filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
				return true;
			} else {
				return false;
			}
		}

		/**
		 * Send Email
		 *
		 * @param string $email
		 * @param string $subject
		 * @param string $body
		 * @return array
		 */
		public static function send($email, $subject, $body)
		{
			// smtp settings
			$smtpSetting = [
				'host'   => getenv('SMTP_HOST'),
				'port'   => getenv('SMTP_PORT'),
				'secure' => getenv('SMTP_SECURE'),
				'email'  => getenv('SMTP_USER'),
				'pass'   => getenv('SMTP_PASS')
			];

			$mail = new \PHPMailer\PHPMailer\PHPMailer();
			$mail->setLanguage(Main::getLocale());
			$mail->IsSMTP();
			$mail->SMTPAuth = true;
			// $mail->SMTPDebug = 2;
			$mail->SMTPSecure = $smtpSetting['secure'];
			$mail->Host = $smtpSetting['host'];
			$mail->Port = $smtpSetting['port'];
			$mail->Username = $smtpSetting['email'];
			$mail->Password = $smtpSetting['pass'];
			$mail->SetFrom($mail->Username, getenv('APP_NAME'));
			$mail->Subject = $subject;
			$mail->MsgHTML($body);
			$mail->AddAddress($email, getenv('APP_NAME'));
			$mail->CharSet = 'UTF-8';
			$mail->SMTPOptions = [
				'ssl' => [
					'verify_peer'       => false,
					'verify_peer_name'  => false,
					'allow_self_signed' => true
				]
			];
			if (!$mail->Send()) {
				return [
					'status'  => false,
					'message' => $mail->ErrorInfo
				];
			} else {
				return [
					'status' => true
				];
			}
		}
	}

?>

<?php
/******************************************************************************
 * StudyConnect
 *
 * Module   : Core
 * File     : mailer.php
 * Purpose  : Creates and configures PHPMailer.
 *
 * Version  : 1.0
 * Updated  : 15-Jul-2026
 ******************************************************************************/

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Creates a configured PHPMailer instance.
 *
 * @return PHPMailer
 */
function createMailer(): PHPMailer
{
    $config = require __DIR__ . '/mail_config.php';

    $mail = new PHPMailer(true);

    $mail->isSMTP();

    $mail->Host = $config['host'];

    $mail->SMTPAuth = true;

    $mail->Username = $config['username'];

    $mail->Password = $config['password'];

    $mail->SMTPSecure = $config['encryption'];

    $mail->Port = $config['port'];
	
	$mail->CharSet = 'UTF-8';

	$mail->Encoding = 'base64';

	$mail->isHTML(true);

    $mail->CharSet = 'UTF-8';

    $mail->isHTML(true);

    $mail->setFrom(
        $config['from_email'],
        $config['from_name']
    );

    return $mail;
}
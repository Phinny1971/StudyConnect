<?php
/******************************************************************************
 * StudyConnect
 *
 * Module   : Core
 * File     : email_helper.php
 * Purpose  : Reusable email helper functions.
 *
 * Version  : 1.0
 * Updated  : 15-Jul-2026
 ******************************************************************************/

require_once __DIR__ . '/mailer.php';

/**
 * Renders an email template.
 *
 * @param string $template Template name (without .php)
 * @param array  $data     Template data
 *
 * @return string
 */
function renderEmailTemplate(
    string $template,
    array $data = []
): string
{
    extract($data, EXTR_SKIP);

    ob_start();

    require __DIR__ . '/email_templates/header.php';

    require __DIR__ .
        '/email_templates/' .
        $template .
        '.php';

    require __DIR__ . '/email_templates/footer.php';

    return ob_get_clean();
}

/**
 * Sends an email.
 *
 * @param string $toEmail
 * @param string $toName
 * @param string $subject
 * @param string $html
 *
 * @return bool
 */
function sendEmail(
    string $toEmail,
    string $toName,
    string $subject,
    string $html
): bool
{
    try
    {
        $mail = createMailer();

        $mail->addAddress(
            $toEmail,
            $toName
        );

        $mail->Subject = $subject;

        $mail->Body = $html;

        $mail->send();

        return true;
    }
    catch (Throwable $e)
    {
        error_log(
            'Email Error: ' .
            $e->getMessage()
        );

        return false;
    }
	
}

/**
 * Sends a password reset email.
 *
 * @param string $email
 * @param string $displayName
 * @param string $temporaryPassword
 *
 * @return bool
 */
function sendPasswordResetEmail(
    string $email,
    string $displayName,
    string $temporaryPassword
): bool
{

    $subject = 'StudyConnect - Password Reset';

    $html = renderEmailTemplate(
        'password_reset',
        [
            'displayName'       => $displayName,
            'temporaryPassword' => $temporaryPassword
        ]
    );

    return sendEmail(
        $email,
        $displayName,
        $subject,
        $html
    );
}


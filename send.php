<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

date_default_timezone_set('Europe/Budapest');

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('X-Robots-Tag: noindex, nofollow');
header('Cache-Control: no-store');

function json_out(bool $ok, string $error = '', int $status = 200): void
{
    http_response_code($status);
    echo json_encode(
        $ok ? ['ok' => true] : ['ok' => false, 'error' => $error],
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );
    exit;
}

function text_length(string $value): int
{
    return function_exists('mb_strlen') ? mb_strlen($value, 'UTF-8') : strlen($value);
}

function is_hungarian_phone(string $phone): bool
{
    $compact = preg_replace('/[\s().\/-]+/', '', $phone);
    if ($compact === null) {
        return false;
    }

    return preg_match(
        '/^(?:\+36|0036|06)(?:1\d{7}|[2-9]\d{7}|(?:20|30|31|50|70)\d{7})$/',
        $compact
    ) === 1;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    json_out(false, 'Ez a végpont csak űrlapküldést fogad.', 405);
}

// CAPTCHA nélküli, felhasználók számára láthatatlan spamcsapda.
if (trim((string) ($_POST['website'] ?? '')) !== '') {
    json_out(true);
}

$name = trim((string) ($_POST['name'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$phone = trim((string) ($_POST['phone'] ?? ''));
$subject_key = trim((string) ($_POST['subject'] ?? ''));
$description = trim((string) ($_POST['description'] ?? ''));

$subjects = [
    'felujitas' => 'Felújítás',
    'bontas' => 'Bontás',
    'butor-osszeszereles' => 'Bútor összeszerelés',
    'karbantartas' => 'Karbantartás',
    'egyeb' => 'Egyéb',
];

if (
    $name === ''
    || $email === ''
    || $phone === ''
    || $subject_key === ''
    || $description === ''
) {
    json_out(false, 'Minden mező kitöltése kötelező.', 422);
}

if (text_length($name) > 100) {
    json_out(false, 'A név legfeljebb 100 karakter lehet.', 422);
}

if (
    text_length($email) > 254
    || !filter_var($email, FILTER_VALIDATE_EMAIL)
) {
    json_out(false, 'Kérjük, adjon meg egy érvényes e-mail-címet.', 422);
}

if (text_length($phone) > 40 || !is_hungarian_phone($phone)) {
    json_out(
        false,
        'Kérjük, adjon meg egy érvényes magyar telefonszámot, például: +36 30 123 4567.',
        422
    );
}

if (!array_key_exists($subject_key, $subjects)) {
    json_out(false, 'Kérjük, válasszon érvényes tárgyat.', 422);
}

if (text_length($description) < 10 || text_length($description) > 3000) {
    json_out(false, 'A leírás 10 és 3000 karakter közötti lehet.', 422);
}

$name = str_replace(["\r", "\n"], ' ', $name);
$subject = $subjects[$subject_key];

require_once __DIR__ . '/lib/phpmailer/PHPMailer.php';
require_once __DIR__ . '/lib/phpmailer/SMTP.php';
require_once __DIR__ . '/lib/phpmailer/Exception.php';

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

$mail = null;

try {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = SMTP_HOST;
    $mail->SMTPAuth = true;
    $mail->Username = SMTP_USER;
    $mail->Password = SMTP_PASS;
    $mail->Port = (int) SMTP_PORT;
    $mail->CharSet = 'UTF-8';
    $mail->Timeout = 15;

    if ($mail->Port === 465) {
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    } elseif ($mail->Port === 25) {
        $mail->SMTPSecure = '';
        $mail->SMTPAutoTLS = false;
    } else {
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    }

    $mail->setFrom(MAIL_FROM, 'Bontó Brigád — Kapcsolatfelvétel');
    $mail->addAddress(MAIL_TO);
    $mail->addReplyTo($email, $name);

    $mail->Subject = $subject;
    $mail->Body = implode("\n", [
        'Új kapcsolatfelvétel érkezett a bontobrigad.hu weboldalról.',
        '',
        'Név: ' . $name,
        'E-mail: ' . $email,
        'Telefon: ' . $phone,
        'Tárgy: ' . $subject,
        '',
        'Leírás:',
        $description,
        '',
        'Elküldve: ' . date('Y-m-d H:i:s'),
    ]);
    $mail->isHTML(false);
    $mail->send();

    json_out(true);
} catch (Exception $exception) {
    $mailer_error = $mail instanceof PHPMailer
        ? $mail->ErrorInfo
        : 'PHPMailer initialization failed';
    error_log('Bonto Brigad mailer error: ' . $mailer_error);
    json_out(
        false,
        'Az üzenet küldése most nem sikerült. Kérjük, próbálja újra később, vagy hívjon minket telefonon.',
        500
    );
}

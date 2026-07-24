<?php
declare(strict_types=1);

/**
 * A GitHub Actions környezeti változóiból állítja elő az ignorált config.php-t.
 * Az értékeket szándékosan nem írja a standard kimenetre.
 */

$stringKeys = [
    'SMTP_HOST',
    'SMTP_USER',
    'SMTP_PASS',
    'MAIL_FROM',
    'MAIL_TO',
];

$values = [];
$missing = [];

foreach ($stringKeys as $key) {
    $value = getenv($key);

    if ($value === false || $value === '') {
        $missing[] = $key;
        continue;
    }

    $values[$key] = $value;
}

$smtpPort = getenv('SMTP_PORT');
if ($smtpPort === false || $smtpPort === '') {
    $missing[] = 'SMTP_PORT';
} elseif (
    filter_var(
        $smtpPort,
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 1, 'max_range' => 65535]]
    ) === false
) {
    fwrite(STDERR, "A SMTP_PORT értéke csak 1 és 65535 közötti egész szám lehet.\n");
    exit(1);
} else {
    $values['SMTP_PORT'] = (int) $smtpPort;
}

if ($missing !== []) {
    fwrite(
        STDERR,
        'Hiányzó kötelező környezeti változók: ' . implode(', ', $missing) . "\n"
    );
    exit(1);
}

foreach (['MAIL_FROM', 'MAIL_TO'] as $emailKey) {
    if (filter_var($values[$emailKey], FILTER_VALIDATE_EMAIL) === false) {
        fwrite(STDERR, "A {$emailKey} értéke nem érvényes e-mail-cím.\n");
        exit(1);
    }
}

$lines = [
    '<?php',
    'declare(strict_types=1);',
    '',
    '// Automatikusan generált fájl. Ne commitolja a repositoryba.',
    '',
    'const SMTP_HOST = ' . var_export($values['SMTP_HOST'], true) . ';',
    'const SMTP_PORT = ' . var_export($values['SMTP_PORT'], true) . ';',
    'const SMTP_USER = ' . var_export($values['SMTP_USER'], true) . ';',
    'const SMTP_PASS = ' . var_export($values['SMTP_PASS'], true) . ';',
    'const MAIL_FROM = ' . var_export($values['MAIL_FROM'], true) . ';',
    'const MAIL_TO = ' . var_export($values['MAIL_TO'], true) . ';',
    '',
];

$target = dirname(__DIR__) . '/config.php';
$written = file_put_contents($target, implode("\n", $lines), LOCK_EX);

if ($written === false) {
    fwrite(STDERR, "A config.php fájl nem hozható létre.\n");
    exit(1);
}

chmod($target, 0600);
fwrite(STDOUT, "A config.php sikeresen létrejött.\n");

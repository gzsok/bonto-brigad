<?php
declare(strict_types=1);

/*
 * Másolja ezt a fájlt config.php néven a helyi fejlesztéshez, majd töltse ki
 * a saját SMTP-adataival. A config.php Git által figyelmen kívül van hagyva.
 */

const SMTP_HOST = 'smtp.example.com';
const SMTP_PORT = 587;
const SMTP_USER = 'felhasznalo@example.com';
const SMTP_PASS = 'smtp-jelszo';
const MAIL_FROM = 'info@example.com';
const MAIL_TO = 'info@example.com';

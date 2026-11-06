<?php
define('CLI_SCRIPT', true);
require(__DIR__ . '/../../public/config.php');

global $CFG, $DB;

$to = isset($argv[1]) ? $argv[1] : 'januarzidane6@gmail.com';

echo "========================================\n";
echo "LUMINA LMS - EMAIL SENDING TEST\n";
echo "========================================\n";
echo "SMTP Host   : " . ($CFG->smtphosts ?: '(none - using PHP mail)') . "\n";
echo "SMTP User   : " . ($CFG->smtpuser ?: '(none)') . "\n";
echo "SMTP Secure : " . ($CFG->smtpsecure ?: '(none)') . "\n";
echo "From        : " . ($CFG->noreplyaddress ?: 'noreply@localhost') . "\n";
echo "Destination : " . $to . "\n";
echo "----------------------------------------\n";

if (empty($CFG->smtphosts) || empty($CFG->smtppass) || $CFG->smtppass === 'your_16_digit_app_password_here') {
    echo "[PERINGATAN] SMTP_PASS di file .env masih default ('your_16_digit_app_password_here')!\n";
    echo "Silakan edit file .env dan isi SMTP_PASS dengan Google App Password Anda.\n";
    echo "========================================\n";
    exit(1);
}

$user = new stdClass();
$user->email = $to;
$user->id = 999999;
$user->firstname = 'Januar';
$user->lastname = 'Zidane';
$user->maildisplay = true;
$user->mailformat = 1;

$noreply = \core_user::get_noreply_user();
$subject = "Tes Pengiriman Email Lumina LMS (OTP Readiness)";
$body = "Halo Januar,\n\nIni adalah email uji coba dari Lumina LMS.\nJika email ini sampai ke inbox Anda, berarti konfigurasi SMTP .env sudah 100% aktif dan email OTP 6-digit siap terkirim secara instan!\n\nWaktu kirim: " . date('Y-m-d H:i:s') . "\n\nSalam,\nLumina IT Support";

echo "Sedang mencoba mengirim email ke $to ...\n";

// Enable debugging if needed
$CFG->debugsmtp = 1;
$CFG->debugdeveloper = 1;

$result = email_to_user($user, $noreply, $subject, $body, nl2br($body));

if ($result) {
    echo "\n[SUKSES] Email berhasil dikirim ke $to!\n";
} else {
    echo "\n[GAGAL] Pengiriman email gagal. Periksa kembali username/password di .env.\n";
}
echo "========================================\n";


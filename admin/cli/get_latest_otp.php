<?php
define('CLI_SCRIPT', true);
require(__DIR__ . '/../../public/config.php');

global $DB;
$sql = "SELECT m.secret, m.timecreated, u.username, u.email 
        FROM {tool_mfa} m 
        JOIN {user} u ON u.id = m.userid 
        WHERE m.factor = 'email' AND NOT m.label = u.email 
        ORDER BY m.timecreated DESC 
        LIMIT 1";

$record = $DB->get_record_sql($sql);

if ($record) {
    echo "========================================\n";
    echo "LUMINA LMS - KODE OTP EMAIL TERAKHIR\n";
    echo "========================================\n";
    echo "User     : " . $record->username . " (" . $record->email . ")\n";
    echo "Kode OTP : " . $record->secret . "\n";
    echo "Waktu    : " . date('Y-m-d H:i:s', $record->timecreated) . "\n";
    echo "Status   : Aktif (berlaku 30 menit)\n";
    echo "========================================\n";
} else {
    echo "Belum ada kode OTP email yang dibuat.\n";
}


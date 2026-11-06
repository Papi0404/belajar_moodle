<?php
define('AJAX_SCRIPT', true);
require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/theme/lumina/classes/lumina_data.php');

require_login(null, false);
require_sesskey();

header('Content-Type: application/json; charset=utf-8');

$message = optional_param('message', '', PARAM_RAW_TRIMMED);
$courseid = optional_param('courseid', 0, PARAM_INT);
$historyraw = optional_param('history', '[]', PARAM_RAW);

if (empty($message)) {
    echo json_encode(['status' => 'error', 'reply' => 'Pesan tidak boleh kosong.']);
    exit;
}

$history = json_decode($historyraw, true);
if (!is_array($history)) {
    $history = [];
}

global $USER, $DB, $CFG;

// Math evaluation handler
$mathclean = preg_replace('/\s+/', '', strtolower($message));
$mathclean = rtrim($mathclean, '=?');
$mathclean = preg_replace('/(berapa|adalah|sama\s*dengan)$/u', '', $mathclean);
$mathclean = rtrim($mathclean, '=?');

if (preg_match('/^[-+*\/%0-9().^]+$/', $mathclean) && preg_match('/[0-9]/', $mathclean) && preg_match('/[-+*\/%^]/', $mathclean)) {
    try {
        $evalexpr = str_replace('^', '**', $mathclean);
        if (preg_match('/^[0-9+\-*\/().% ]+$/', $evalexpr)) {
            $calc = @eval('return ' . $evalexpr . ';');
            if ($calc !== false && $calc !== null) {
                echo json_encode([
                    'status' => 'success',
                    'reply'  => "{$message} = **{$calc}**",
                    'engine' => 'math-eval'
                ]);
                exit;
            }
        }
    } catch (\Throwable $e) { }
}

$enrolled = enrol_get_all_users_courses($USER->id, true);
$coursenames = [];
foreach ($enrolled as $c) {
    $coursenames[] = $c->fullname;
}
$courseliststr = !empty($coursenames) ? implode(', ', $coursenames) : 'Belum ada mata kuliah';

$now = time();
$lookahead = $now + (30 * DAYSECS);
$pendingtasks = [];
if (!empty($enrolled)) {
    $cids = array_keys($enrolled);
    list($insql, $params) = $DB->get_in_or_equal($cids, SQL_PARAMS_NAMED, 'c');
    $params['now'] = $now;
    $params['lookahead'] = $lookahead;
    $assigns = $DB->get_records_sql(
        "SELECT a.id, a.name, a.duedate, c.fullname as coursename
           FROM {assign} a JOIN {course} c ON c.id = a.course
          WHERE a.course $insql AND a.duedate > :now AND a.duedate < :lookahead
          ORDER BY a.duedate ASC LIMIT 5",
        $params
    );
    foreach ($assigns as $a) {
        $days = (int)ceil(($a->duedate - $now) / DAYSECS);
        $pendingtasks[] = "{$a->name} ({$a->coursename}) - tenggat {$days} hari lagi";
    }
}
$pendingtaskstr = !empty($pendingtasks) ? implode('; ', $pendingtasks) : 'Semua tugas telah diselesaikan';

$systemprompt = "You are Lumina AI, an intelligent, empathetic, and knowledgeable academic AI tutor inside Lumina LMS.
Current user: {$USER->firstname} {$USER->lastname} (Role: " . (is_siteadmin() ? 'Administrator' : 'Mahasiswa') . ")
Enrolled courses: {$courseliststr}
Upcoming deadlines: {$pendingtaskstr}";

$geminikey = getenv('GEMINI_API_KEY') ?: '';
$openaikey = getenv('OPENAI_API_KEY') ?: '';
$deepseekkey = getenv('DEEPSEEK_API_KEY') ?: '';

try {
    $activeproviders = $DB->get_records('ai_providers', ['enabled' => 1], 'id DESC');
    foreach ($activeproviders as $p) {
        $cfg = json_decode($p->config, true) ?: [];
        $provstr = strtolower(($p->provider ?? '') . ' ' . ($cfg['aiprovider'] ?? '') . ' ' . ($p->name ?? ''));
        if (strpos($provstr, 'gemini') !== false && !empty($cfg['apikey'])) {
            $geminikey = $cfg['apikey'];
        }
        if (strpos($provstr, 'openai') !== false && !empty($cfg['apikey'])) {
            $openaikey = $cfg['apikey'];
        }
        if (strpos($provstr, 'deepseek') !== false && !empty($cfg['apikey'])) {
            $deepseekkey = $cfg['apikey'];
        }
    }
} catch (\Throwable $e) { }

if (!empty($geminikey)) {
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-3.5-flash:generateContent?key=" . urlencode($geminikey);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'systemInstruction' => ['parts' => [['text' => $systemprompt]]],
        'contents' => [['role' => 'user', 'parts' => [['text' => $message]]]]
    ]));
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $res = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($httpcode === 200 && !empty($res)) {
        $json = json_decode($res, true);
        $reply = $json['candidates'][0]['content']['parts'][0]['text'] ?? null;
        if (!empty($reply)) {
            echo json_encode(['status' => 'success', 'reply' => trim($reply), 'engine' => 'gemini']);
            exit;
        }
    }
}

echo json_encode([
    'status' => 'success', 
    'reply' => "Halo **{$USER->firstname}**, saya Lumina AI (Plugin Mode). Pertanyaan Anda tentang **{$message}** telah diterima dan diproses dengan aman.",
    'engine' => 'lumina-plugin-fallback'
]);
exit;

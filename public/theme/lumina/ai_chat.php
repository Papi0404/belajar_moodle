<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

define('AJAX_SCRIPT', true);

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/theme/lumina/classes/lumina_data.php');

// Ensure user is logged in
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

// 1. Math / Expression quick calculation handler (e.g. 5+5, 10*2.5, etc.)
$mathclean = preg_replace('/\s+/', '', strtolower($message));
$mathclean = rtrim($mathclean, '=?');
// Also strip common trailing indonesian question words like 'berapa'
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

// 2. Gather contextual academic data for this user
$enrolled = enrol_get_all_users_courses($USER->id, true);
$coursenames = [];
foreach ($enrolled as $c) {
    $coursenames[] = $c->fullname;
}
$courseliststr = !empty($coursenames) ? implode(', ', $coursenames) : 'Belum ada mata kuliah';

// Upcoming assignments & quizzes
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
$pendingtaskstr = !empty($pendingtasks) ? implode('; ', $pendingtasks) : 'Semua tugas telah diselesaikan / belum ada tugas terbuka';

// System prompt for LLMs
$systemprompt = "You are Lumina AI, an intelligent, empathetic, and knowledgeable academic AI tutor inside Lumina LMS (Moodle).
Current user: {$USER->firstname} {$USER->lastname} (Role: " . (is_siteadmin() ? 'Administrator' : 'Mahasiswa') . ")
Enrolled courses: {$courseliststr}
Upcoming deadlines: {$pendingtaskstr}

Guidelines:
1. Always respond naturally and conversationally in the language the user asked in (Indonesian by default, or English).
2. Answer direct questions directly and concisely (for example, if asked '5+5 berapa?', answer '5 + 5 = 10' first before elaborating if needed).
3. Be friendly, helpful, and pedagogically clear like ChatGPT.
4. Use formatted Markdown with bold headings, bullet points, and code blocks (```) when appropriate.
5. If the user asks about their courses or deadlines, provide their real academic context.";

// -----------------------------------------------------------------------------
// Auto-detect credentials dynamically from Moodle's mdl_ai_providers table + .env
// Whenever an admin adds or enables an AI provider in Moodle settings,
// Lumina AI will automatically recognize and use it immediately!
// -----------------------------------------------------------------------------
$geminikey = getenv('GEMINI_API_KEY') ?: '';
$geminimodel_configured = '';
$openaikey = getenv('OPENAI_API_KEY') ?: '';
$openaimodel_configured = '';
$deepseekkey = getenv('DEEPSEEK_API_KEY') ?: '';
$ollamaendpoint = getenv('OLLAMA_ENDPOINT') ?: '';

try {
    $activeproviders = $DB->get_records('ai_providers', ['enabled' => 1], 'id DESC');
    foreach ($activeproviders as $p) {
        $cfg = json_decode($p->config, true) ?: [];
        $actcfg = json_decode($p->actionconfig, true) ?: [];
        $provstr = strtolower(($p->provider ?? '') . ' ' . ($cfg['aiprovider'] ?? '') . ' ' . ($p->name ?? ''));

        // Gemini
        if (strpos($provstr, 'gemini') !== false && !empty($cfg['apikey'])) {
            $geminikey = $cfg['apikey'];
            if (!empty($actcfg['core_ai\\aiactions\\generate_text']['settings']['model'])) {
                $geminimodel_configured = $actcfg['core_ai\\aiactions\\generate_text']['settings']['model'];
            }
        }
        // OpenAI
        if (strpos($provstr, 'openai') !== false && !empty($cfg['apikey'])) {
            $openaikey = $cfg['apikey'];
            if (!empty($actcfg['core_ai\\aiactions\\generate_text']['settings']['model'])) {
                $openaimodel_configured = $actcfg['core_ai\\aiactions\\generate_text']['settings']['model'];
            }
        }
        // DeepSeek
        if (strpos($provstr, 'deepseek') !== false && !empty($cfg['apikey'])) {
            $deepseekkey = $cfg['apikey'];
        }
        // Ollama
        if (strpos($provstr, 'ollama') !== false && !empty($cfg['endpoint'])) {
            $ollamaendpoint = $cfg['endpoint'];
        }
    }
} catch (\Throwable $e) { }

// -----------------------------------------------------------------------------
// Engine 1: Google Gemini API (auto-read from Moodle AI Providers or .env)
// -----------------------------------------------------------------------------
if (!empty($geminikey)) {
    $candidatemodels = array_values(array_filter(array_unique([
        $geminimodel_configured,
        'gemini-3.5-flash',
        'gemini-3.5-flash-lite',
        'gemini-3.1-flash-lite',
        'gemini-flash-latest',
        'gemini-3.8-flash',
    ])));

    $contents = [];
    foreach ($history as $h) {
        if (!empty($h['text']) && !empty($h['role'])) {
            $role = ($h['role'] === 'user') ? 'user' : 'model';
            $contents[] = [
                'role' => $role,
                'parts' => [['text' => $h['text']]]
            ];
        }
    }
    $contents[] = [
        'role' => 'user',
        'parts' => [['text' => $message]]
    ];

    $payload = [
        'systemInstruction' => [
            'parts' => [['text' => $systemprompt]]
        ],
        'contents' => $contents,
        'generationConfig' => [
            'temperature' => 0.7,
            'maxOutputTokens' => 1500,
        ]
    ];
    $jsonpayload = json_encode($payload);

    foreach ($candidatemodels as $model) {
        $geminiurl = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . urlencode($geminikey);

        $ch = curl_init($geminiurl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonpayload);
        curl_setopt($ch, CURLOPT_TIMEOUT, 12);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        $res = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpcode === 200 && !empty($res)) {
            $json = json_decode($res, true);
            $reply = $json['candidates'][0]['content']['parts'][0]['text'] ?? null;
            if (!empty($reply)) {
                echo json_encode(['status' => 'success', 'reply' => trim($reply), 'engine' => $model]);
                exit;
            }
        }
    }
}

// -----------------------------------------------------------------------------
// Engine 2: DeepSeek API (auto-read from Moodle AI Providers or .env)
// -----------------------------------------------------------------------------
if (!empty($deepseekkey)) {
    $messages = [
        ['role' => 'system', 'content' => $systemprompt]
    ];
    foreach ($history as $h) {
        if (!empty($h['text']) && !empty($h['role'])) {
            $role = ($h['role'] === 'user') ? 'user' : 'assistant';
            $messages[] = ['role' => $role, 'content' => $h['text']];
        }
    }
    $messages[] = ['role' => 'user', 'content' => $message];

    $ch = curl_init('https://api.deepseek.com/chat/completions');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $deepseekkey
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'model' => 'deepseek-chat',
        'messages' => $messages,
        'temperature' => 0.7,
    ]));
    curl_setopt($ch, CURLOPT_TIMEOUT, 12);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
    $res = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpcode === 200 && !empty($res)) {
        $json = json_decode($res, true);
        $reply = $json['choices'][0]['message']['content'] ?? null;
        if (!empty($reply)) {
            echo json_encode(['status' => 'success', 'reply' => trim($reply), 'engine' => 'deepseek']);
            exit;
        }
    }
}

// -----------------------------------------------------------------------------
// Engine 3: OpenAI API (auto-read from Moodle AI Providers or .env)
// -----------------------------------------------------------------------------
if (!empty($openaikey)) {
    $messages = [
        ['role' => 'system', 'content' => $systemprompt]
    ];
    foreach ($history as $h) {
        if (!empty($h['text']) && !empty($h['role'])) {
            $role = ($h['role'] === 'user') ? 'user' : 'assistant';
            $messages[] = ['role' => $role, 'content' => $h['text']];
        }
    }
    $messages[] = ['role' => 'user', 'content' => $message];

    $ch = curl_init('https://api.openai.com/v1/chat/completions');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $openaikey
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'model' => 'gpt-4o-mini',
        'messages' => $messages,
        'temperature' => 0.7,
    ]));
    curl_setopt($ch, CURLOPT_TIMEOUT, 12);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
    $res = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpcode === 200 && !empty($res)) {
        $json = json_decode($res, true);
        $reply = $json['choices'][0]['message']['content'] ?? null;
        if (!empty($reply)) {
            echo json_encode(['status' => 'success', 'reply' => trim($reply), 'engine' => 'openai']);
            exit;
        }
    }
}

// -----------------------------------------------------------------------------
// Engine 4: Ollama API (if running)
// -----------------------------------------------------------------------------
$ollamahosts = array_filter([
    $ollamaendpoint,
    'http://127.0.0.1:11434',
    'http://172.20.32.1:11434',
    'http://host.docker.internal:11434'
]);
foreach ($ollamahosts as $host) {
    $ch = curl_init(rtrim($host, '/') . '/api/generate');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'model' => 'llama3.3',
        'prompt' => $message,
        'system' => $systemprompt,
        'stream' => false
    ]));
    curl_setopt($ch, CURLOPT_TIMEOUT, 12);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
    $res = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpcode === 200 && !empty($res)) {
        $json = json_decode($res, true);
        if (!empty($json['response'])) {
            echo json_encode(['status' => 'success', 'reply' => trim($json['response']), 'engine' => 'ollama']);
            exit;
        }
    }
}

// -----------------------------------------------------------------------------
// Engine 5: Conversational Academic Fallback Engine (Direct and Natural)
// -----------------------------------------------------------------------------
$reply = lumina_generate_academic_response($message, $USER, $enrolled, $pendingtasks);
echo json_encode(['status' => 'success', 'reply' => $reply, 'engine' => 'lumina-academic-ai']);
exit;

/**
 * Intelligent Academic Conversational Engine
 */
function lumina_generate_academic_response(string $msg, $user, array $enrolled, array $pendingtasks): string {
    $q = mb_strtolower(trim($msg));
    $name = $user->firstname ?: 'Rekan';

    // 1. Greetings
    if (preg_match('/^(halo|hai|hi|hello|selamat pagi|selamat siang|selamat sore|selamat malam|assalamu)/i', $q)) {
        return "Halo **{$name}**! 👋 Saya **Lumina AI**, asisten akademik personal Anda di Lumina LMS.\n\nSaya siap membantu Anda dengan:\n- 💡 **Menjelaskan materi kuliah** & konsep sulit\n- 📝 **Membantu pengerjaan tugas** & koding (Python, SQL, PHP, Java, JS, dll.)\n- 📅 **Memeriksa jadwal kuliah & tenggat waktu tugas**\n- 📚 **Tips belajar efektif** untuk persiapan ujian\n\nApa topik yang ingin kita bahas hari ini?";
    }

    // 2. Questions about enrolled courses
    if (strpos($q, 'kuliah') !== false && (strpos($q, 'apa saja') !== false || strpos($q, 'daftar') !== false || strpos($q, 'saya') !== false || strpos($q, 'mata kuliah') !== false)) {
        if (empty($enrolled)) {
            return "Saat ini Anda belum terdaftar di mata kuliah aktif. Anda dapat menelusuri katalog kursus untuk memilih mata kuliah.";
        }
        $out = "Berikut adalah daftar **Mata Kuliah Aktif** Anda semester ini:\n\n";
        $i = 1;
        foreach ($enrolled as $c) {
            $pct = \core_completion\progress::get_course_progress_percentage($c, $user->id);
            $pctstr = $pct !== null ? round($pct) . "% progres" : "aktif";
            $out .= "{$i}. 📘 **{$c->fullname}** ({$c->shortname}) — *{$pctstr}*\n";
            $i++;
        }
        $out .= "\nKetik nama mata kuliah jika Anda ingin saya jelaskan topik materinya!";
        return $out;
    }

    // 3. Questions about deadlines / assignments
    if (strpos($q, 'tugas') !== false || strpos($q, 'tenggat') !== false || strpos($q, 'deadline') !== false || strpos($q, 'kuis') !== false) {
        if (empty($pendingtasks)) {
            return "🎉 **Kabar baik!** Saat ini tidak ada tugas atau kuis yang tertunda. Semua tugas dalam 30 hari ke depan telah Anda selesaikan dengan baik! Tetap pertahankan prestasinya.";
        }
        $out = "📅 Berikut adalah daftar **Tenggat Tugas & Kuis Terbuka** Anda:\n\n";
        foreach ($pendingtasks as $task) {
            $out .= "- ⏳ **{$task}**\n";
        }
        $out .= "\n💡 **Tips Lumina:** Mulai kerjakan tugas dengan tenggat paling dekat lebih awal untuk menghindari penumpukan di akhir pekan!";
        return $out;
    }

    // 4. Questions about Database / SQL
    if (strpos($q, 'basis data') !== false || strpos($q, 'database') !== false || strpos($q, 'sql') !== false || strpos($q, 'query') !== false) {
        return "🗄️ **Konsep Basis Data & SQL:**\n\nDalam mata kuliah *Sistem Basis Data Terdistribusi & Big Data*, ada beberapa prinsip inti yang wajib dipahami:\n\n1. **ACID Properties:**\n   - **Atomicity:** Semua operasi berhasil atau batal total.\n   - **Consistency:** Data selalu valid sesuai aturan integrity constraint.\n   - **Isolation:** Transaksi yang berjalan bersamaan tidak saling mengganggu.\n   - **Durability:** Data yang sudah di-commit tersimpan permanen.\n\n2. **Contoh Query SQL (Join & Aggregation):**\n```sql\nSELECT m.nama, COUNT(t.id) AS total_tugas\nFROM mahasiswa m\nLEFT JOIN pengumpulan_tugas t ON m.id = t.mahasiswa_id\nGROUP BY m.id, m.nama\nHAVING COUNT(t.id) > 0;\n```\n\nApakah ada studi kasus query atau topik partisi/sharding database tertentu yang ingin Anda diskusikan?";
    }

    // 5. Questions about Algorithms & Data Structures
    if (strpos($q, 'algoritma') !== false || strpos($q, 'struktur data') !== false || strpos($q, 'sorting') !== false || strpos($q, 'tree') !== false || strpos($q, 'graph') !== false) {
        return "🧠 **Algoritma & Struktur Data:**\n\nStruktur data efisien adalah fondasi komputasi performa tinggi:\n\n- **Array vs Linked List:** Array memiliki akses $O(1)$ via indeks namun ukuran statis, sedangkan Linked List dinamis dengan penyisipan $O(1)$ di ujung.\n- **Binary Search Tree (BST):** Pencarian rata-rata $O(\\log n)$.\n- **Hash Table:** Rata-rata pencarian, insert, delete dalam waktu konstan $O(1)$ menggunakan fungsi hash.\n\n```python\n# Contoh Binary Search dalam Python (O(log n))\ndef binary_search(arr, target):\n    low, high = 0, len(arr) - 1\n    while low <= high:\n        mid = (low + high) // 2\n        if arr[mid] == target:\n            return mid\n        elif arr[mid] < target:\n            low = mid + 1\n        else:\n            high = mid - 1\n    return -1\n```\n\nPerlu bantuan menganalisis kompleksitas Big-O atau implementasi kode tertentu?";
    }

    // 6. Direct natural response
    return "Pertanyaan yang menarik! Mengenai **" . htmlspecialchars($msg) . "**:\n\n" .
           "Silakan tanyakan materi kuliah, konsep teori, atau masalah koding yang sedang Anda pelajari, dan saya akan menjelaskannya secara detail langkah demi langkah!";
}

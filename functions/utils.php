<?php

function sendMessage($chat_id, $text, $withKeyboard = false) {
    global $API_URL;

    $keyboard = [
        'keyboard' => [
            [['text' => '🔁 بحث جديد']],
            [['text' => 'ℹ️ تعليمات'], ['text' => '📢 من نحن']]
        ],
        'resize_keyboard' => true
    ];

    $payload = [
        'chat_id' => $chat_id,
        'text' => $text,
        'parse_mode' => 'Markdown'
    ];

    if ($withKeyboard) {
        $payload['reply_markup'] = json_encode($keyboard);
    }

    file_get_contents($API_URL . "sendMessage?" . http_build_query($payload));
}

// تنسيق النتائج
function formatResponse($matches) {
    $response = "";
    foreach ($matches as $match) {
        $response .= "👤 *الاسم:* " . $match['name'] . "\n";
        $response .= "📞 *الرقم:* " . $match['number'] . "\n";
        $response .= "📍 *العنوان:* " . $match['address'] . "\n";
        if (!empty($match['extra'])) {
            $response .= "🗓️ *معلومة إضافية:* " . $match['extra'] . "\n";
        }
        $response .= "📂 *المصدر:* `" . basename($match['source']) . "`\n\n";
    }
    return $response;
}

// تسجيل عمليات البحث
function logSearch($user_id, $full_name, $query) {
    $entry = "[" . date("Y-m-d H:i:s") . "] ID: $user_id | اسم: $full_name | بحث: $query\n";
    file_put_contents("logs/log.txt", $entry, FILE_APPEND);
}

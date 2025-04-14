<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'vendor/autoload.php';
require 'functions/search.php';
require 'functions/utils.php';
require 'functions/throttle.php';
require 'functions/drive.php'; 

$TOKEN = "8039297495:AAE3KauVxGe2Ggj6AVgcB-se2ZpBm0Wlkjw"
;
$API_URL = "https://api.telegram.org/bot$TOKEN/";

$update = json_decode(file_get_contents("php://input"), true);
if (!$update || !isset($update["message"])) exit;

$chat_id = $update["message"]["chat"]["id"];
$user_id = $update["message"]["from"]["id"];
$first_name = $update["message"]["from"]["first_name"] ?? '';
$last_name = $update["message"]["from"]["last_name"] ?? '';
$full_name = trim($first_name . ' ' . $last_name);
$text = strtolower(trim($update["message"]["text"] ?? ''));

// منع السبام
if (isThrottled($user_id)) {
    sendMessage($chat_id, "⏳ الرجاء الانتظار بضع ثوانٍ قبل إرسال طلب جديد.");
    exit;
}
updateThrottle($user_id);

// أوامر
if ($text === "/start") {
    sendMessage($chat_id, "👋 مرحبًا بك في بوت البحث الذكي!\n\nأرسل رقم أو اسم للبحث:");
    exit;
}

if ($text === "/help") {
    sendMessage($chat_id, "🔍 أرسل رقم يبدأ بـ 77 أو 78 للبحث في قواعد Asiacell وZain\nأو أرسل اسم للبحث في جميع القواعد.");
    exit;
}

if ($text === "/about") {
    sendMessage($chat_id, "🤖 بوت بحث احترافي مبني بـ PHP يستعرض بيانات من Google Drive مباشرة.");
    exit;
}

// تحميل الملفات (فقط إذا ما كانت موجودة)
downloadIfNotExists("1Ju82wGE42iiP1pl0I-wJc8bSI7HEzG-F", "Asiacell_Telecom.xlsx");
downloadIfNotExists("1P2TVZ7JFze5SCAoTAyQtqnHoBOvkXyqe", "Iraq-Zain.csv");
// downloadIfNotExists("FILE_ID_HERE", "Zain_Telecom_Database.xlsx"); ← بعد ما ترسله

logSearch($user_id, $full_name, $text);

$matches = [];

if (preg_match('/^\d+$/', $text)) {
    $last10 = substr($text, -10);
    $prefix = substr($last10, 0, 2);

    if ($prefix === "77") {
        $matches = searchInExcel("data/Asiacell_Telecom.xlsx", $last10);
    } elseif ($prefix === "78") {
        $matches = array_merge(
            searchInExcel("data/Zain_Telecom_Database.xlsx", $last10), // مضافة لاحقًا
            searchInCSV("data/Iraq-Zain.csv", $last10)
        );
    }
} else {
    $matches = array_merge(
        searchInExcel("data/Asiacell_Telecom.xlsx", $text, false),
        searchInExcel("data/Zain_Telecom_Database.xlsx", $text, false), // مضافة لاحقًا
        searchInCSV("data/Iraq-Zain.csv", $text, false)
    );
}

$response = count($matches) > 0 ? formatResponse($matches) : "🚫 لم يتم العثور على نتائج.";
sendMessage($chat_id, $response, true);

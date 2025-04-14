<?php

function downloadIfNotExists($fileId, $saveAs) {
    $filePath = "data/$saveAs";

    if (file_exists($filePath)) return; // موجود مسبقًا؟ لا داعي لتحميله

    $url = "https://docs.google.com/uc?export=download&id=$fileId";
    $content = @file_get_contents($url);

    if ($content === false) {
        error_log("❌ فشل تحميل الملف: $saveAs");
        return;
    }

    file_put_contents($filePath, $content);
}

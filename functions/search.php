<?php
use PhpOffice\PhpSpreadsheet\IOFactory;

// البحث في ملفات Excel
function searchInExcel($file, $query, $byNumber = true) {
    $matches = [];
    $spreadsheet = IOFactory::load($file);

    foreach ($spreadsheet->getSheetIterator() as $sheet) {
        foreach ($sheet->getRowIterator() as $row) {
            $cells = [];

            // ✅ نفعّل قراءة كل الخلايا حتى الفارغة
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            foreach ($cellIterator as $cell) {
                $cells[] = $cell->getValue();
            }

            // ✅ البحث بالرقم (آخر 10 أرقام)
            if ($byNumber && isset($cells[0]) && substr(preg_replace('/\D/', '', $cells[0]), -10) === $query) {
                $matches[] = [
                    'name' => $cells[1] ?? $cells[0],
                    'number' => $cells[0],
                    'address' => $cells[2] ?? '',
                    'extra' => $cells[6] ?? '',
                    'source' => $file
                ];
            }

            // ✅ البحث بالاسم
            elseif (!$byNumber && isset($cells[0]) && stripos($cells[0], $query) !== false) {
                $matches[] = [
                    'name' => $cells[0],
                    'number' => $cells[3] ?? '',
                    'address' => $cells[1] ?? '',
                    'extra' => $cells[6] ?? '',
                    'source' => $file
                ];
            }

            if (count($matches) >= 5) break;
        }

        if (count($matches) >= 5) break;
    }

    return $matches;
}

// البحث في ملفات CSV
function searchInCSV($file, $query, $byNumber = true) {
    $matches = [];
    $lines = file($file);

    foreach ($lines as $line) {
        $cols = explode("\t", trim($line));

        if ($byNumber && isset($cols[0]) && substr(preg_replace('/\D/', '', $cols[0]), -10) === $query) {
            $matches[] = [
                'name' => $cols[1] ?? '',
                'number' => $cols[0],
                'address' => $cols[2] ?? '',
                'extra' => '',
                'source' => $file
            ];
        } elseif (!$byNumber && isset($cols[1]) && stripos($cols[1], $query) !== false) {
            $matches[] = [
                'name' => $cols[1],
                'number' => $cols[0],
                'address' => $cols[2] ?? '',
                'extra' => '',
                'source' => $file
            ];
        }

        if (count($matches) >= 5) break;
    }

    return $matches;
}

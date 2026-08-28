<?php

namespace App\Services;

use ZipArchive;

class SimpleExcelReader
{
    public static function read(string $filePath): array
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        if (in_array($extension, ['csv', 'txt'])) {
            return static::readCsv($filePath);
        }

        if ($extension === 'xlsx') {
            return static::readXlsx($filePath);
        }

        try {
            $rows = static::readXlsx($filePath);
            if (!empty($rows)) {
                return $rows;
            }
        } catch (\Throwable $e) {
            // fallback
        }

        return static::readCsv($filePath);
    }

    public static function readCsv(string $filePath): array
    {
        $rows = [];
        if (($handle = fopen($filePath, 'r')) !== false) {
            $firstLine = fgets($handle);
            if ($firstLine === false) {
                fclose($handle);
                return [];
            }
            rewind($handle);

            $delimiter = ',';
            if (substr_count($firstLine, ';') > substr_count($firstLine, ',')) {
                $delimiter = ';';
            } elseif (substr_count($firstLine, "\t") > substr_count($firstLine, ',')) {
                $delimiter = "\t";
            }

            while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
                if (array_filter($data, fn($v) => trim((string)$v) !== '') !== []) {
                    $rows[] = array_map('trim', $data);
                }
            }
            fclose($handle);
        }
        return $rows;
    }

    public static function readXlsx(string $filePath): array
    {
        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            return static::readCsv($filePath);
        }

        $sharedStrings = [];
        $sharedStringsXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($sharedStringsXml !== false) {
            $xml = @simplexml_load_string($sharedStringsXml);
            if ($xml && isset($xml->si)) {
                foreach ($xml->si as $val) {
                    if (isset($val->t)) {
                        $sharedStrings[] = (string) $val->t;
                    } elseif (isset($val->r)) {
                        $str = '';
                        foreach ($val->r as $r) {
                            $str .= (string) $r->t;
                        }
                        $sharedStrings[] = $str;
                    } else {
                        $sharedStrings[] = '';
                    }
                }
            }
        }

        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        if ($sheetXml === false) {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $filename = $zip->getNameIndex($i);
                if (str_contains($filename, 'xl/worksheets/sheet')) {
                    $sheetXml = $zip->getFromName($filename);
                    break;
                }
            }
        }

        $zip->close();

        if ($sheetXml === false) {
            return static::readCsv($filePath);
        }

        $xml = @simplexml_load_string($sheetXml);
        if (!$xml || !isset($xml->sheetData)) {
            return static::readCsv($filePath);
        }

        $rows = [];
        foreach ($xml->sheetData->row as $r) {
            $row = [];
            foreach ($r->c as $c) {
                $cellRef = (string) $c['r'];
                $type = (string) $c['t'];
                $val = (string) $c->v;

                if ($type === 's' && isset($sharedStrings[(int) $val])) {
                    $cellVal = $sharedStrings[(int) $val];
                } elseif ($type === 'inlineStr' && isset($c->is->t)) {
                    $cellVal = (string) $c->is->t;
                } else {
                    $cellVal = $val;
                }

                $colStr = preg_replace('/[0-9]/', '', $cellRef);
                $colIdx = 0;
                for ($i = 0; $i < strlen($colStr); $i++) {
                    $colIdx = $colIdx * 26 + (ord(strtoupper($colStr[$i])) - ord('A') + 1);
                }
                $colIdx -= 1;

                $row[$colIdx] = trim($cellVal);
            }

            if (!empty($row)) {
                ksort($row);
                $maxIdx = max(array_keys($row));
                $fullRow = [];
                for ($i = 0; $i <= $maxIdx; $i++) {
                    $fullRow[$i] = $row[$i] ?? '';
                }
                if (array_filter($fullRow, fn($v) => trim((string)$v) !== '') !== []) {
                    $rows[] = $fullRow;
                }
            }
        }

        return $rows;
    }
}

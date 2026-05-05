<?php
error_reporting(0);
ini_set('display_errors', 0);
ob_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['content'])) {
    $content = $_POST['content'];
    $rawName = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $_POST['filename'] ?: 'document');
    $type = $_POST['type'] ?: 'docx';
    $fullFilename = $rawName . '.' . $type;

    if ($type === 'docx') {
        $lines = explode("\n", strip_tags($content));
        $xmlBody = "";
        foreach ($lines as $line) {
            $xmlBody .= "<w:p><w:r><w:t>" . htmlspecialchars($line, ENT_XML1, "UTF-8") . "</w:t></w:r></w:p>";
        }

        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
               '<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">' .
               '<w:body>' . $xmlBody . '</w:body></w:document>';

        $contentTypes = '<?xml version="1.0" encoding="UTF-8"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/></Types>';
        $rels = '<?xml version="1.0" encoding="UTF-8"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/></Relationships>';

        $zip = new ZipArchive();
        $temp = tempnam(sys_get_temp_dir(), 'doc_');

        if ($zip->open($temp, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            $zip->addFromString('word/document.xml', $xml);
            $zip->addFromString('[Content_Types].xml', $contentTypes);
            $zip->addFromString('_rels/.rels', $rels);
            $zip->close();

            ob_end_clean();
            header('Content-Description: File Transfer');
            header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
            header('Content-Disposition: attachment; filename="' . $fullFilename . '"');
            header('Content-Transfer-Encoding: binary');
            header('Content-Length: ' . filesize($temp));
            
            readfile($temp);
            unlink($temp);
            exit;
        }
    } elseif ($type === 'pdf') {
        ob_end_clean();
        header('Content-Type: text/html; charset=UTF-8');
        echo "<!DOCTYPE html><html><head><title>$fullFilename</title><style>body{font-family:sans-serif;padding:50px;line-height:1.6;}</style></head><body onload='window.print()'><div>" . nl2br($content) . "</div></body></html>";
        exit;
    }
}


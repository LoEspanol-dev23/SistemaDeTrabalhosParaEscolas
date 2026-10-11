<?php
$upload_dir = 'uploads/';
$zip_filename = 'todos_os_trabalhos_' . date('Y-m-d_H-i-s') . '.zip';

if (!is_dir($upload_dir)) {
    die("Pasta de uploads não encontrada.");
}

$zip = new ZipArchive();
if ($zip->open($zip_filename, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
    die("Não foi possível criar o arquivo ZIP.");
}

$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($upload_dir, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::LEAVES_ONLY
);

foreach ($files as $name => $file) {
    if (!$file->isDir()) {
        $filePath = $file->getRealPath();
        $relativePath = substr($filePath, strlen(realpath($upload_dir)) + 1);
        $zip->addFile($filePath, $relativePath);
    }
}

$zip->close();

if (file_exists($zip_filename)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="' . basename($zip_filename) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($zip_filename));
    readfile($zip_filename);
    unlink($zip_filename); // Remove o arquivo zip temporário do servidor após o download
    exit;
} else {
    echo "Erro ao gerar o arquivo ZIP.";
}
?>

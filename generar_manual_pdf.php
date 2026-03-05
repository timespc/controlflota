<?php
/**
 * Genera MANUAL_USUARIO.pdf a partir de public/MANUAL_USUARIO.html
 * Requiere: composer require dompdf/dompdf
 * Uso: php generar_manual_pdf.php
 */

$dirRoot = __DIR__;
$htmlPath = $dirRoot . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'MANUAL_USUARIO.html';
$pdfPath  = $dirRoot . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'MANUAL_USUARIO.pdf';

if (!is_file($htmlPath)) {
    fwrite(STDERR, "No se encuentra public/MANUAL_USUARIO.html\n");
    exit(1);
}

$autoload = $dirRoot . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
if (!is_file($autoload)) {
    fwrite(STDERR, "Ejecutá 'composer install' en la raíz del proyecto.\n");
    exit(1);
}
require $autoload;

if (!class_exists(\Dompdf\Dompdf::class)) {
    fwrite(STDERR, "Para generar el PDF instalá dompdf: composer require dompdf/dompdf\n");
    fwrite(STDERR, "Alternativa: abrí public/MANUAL_USUARIO.html en el navegador y usá Imprimir > Guardar como PDF.\n");
    exit(1);
}

$html = file_get_contents($htmlPath);
if ($html === false) {
    fwrite(STDERR, "No se pudo leer el archivo HTML.\n");
    exit(1);
}

$dompdf = new \Dompdf\Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$pdf = $dompdf->output();

if (file_put_contents($pdfPath, $pdf) === false) {
    fwrite(STDERR, "No se pudo escribir public/MANUAL_USUARIO.pdf\n");
    exit(1);
}

echo "PDF generado: public/MANUAL_USUARIO.pdf\n";
exit(0);

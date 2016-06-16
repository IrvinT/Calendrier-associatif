<?php

include_once(_PS_MODULE_DIR_.'filesupload/filesupload.php');

$file = Tools::getValue('name');

if (file_exists($file)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename='.basename($file));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    ob_clean();
    flush();
    //Attention au XSS mettre un basename pour lire le rep uploads du module
    readfile(basename($file));
    exit;
}
?>

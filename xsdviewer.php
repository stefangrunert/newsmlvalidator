<?php
$xsl = file_get_contents(dirname(__FILE__) . "/lib/vendor/xs3p/xs3p.xsl");
$standardVersion = '2.20';
if (isset($_GET['standardVersion'])) {
    if (in_array($_GET['standardVersion'], array(
        '2.9', '2.10', '2.11', '2.12', '2.13', '2.14', '2.15', '2.16', '2.17', '2.18', '2.19', '2.20'
    ))) {
        $standardVersion = $_GET['standardVersion'];
    }
}
$filename = dirname(__FILE__) . "/doc/xsddoc/NewsML-G2_{$standardVersion}.html";
header('Content-type: application/xhtml+xml; charset=utf-8');
if (file_exists($filename)) {
    die(file_get_contents($filename));
}
$xml = file_get_contents(dirname(__FILE__) . "/lib/xsd/newsml/NewsML-G2_{$standardVersion}-spec-All-Power.xsd");
$xslDoc = new DOMDocument();
$xslDoc->loadXML($xsl);
$xmlDoc = new DOMDocument();
$xmlDoc->loadXML($xml);
$proc = new XSLTProcessor();
$proc->importStylesheet($xslDoc);
$html = $proc->transformToXML($xmlDoc);
if (is_writable(dirname($filename))) {
    file_put_contents($filename, $html);
    chmod($filename, 0777);
}
die($html);

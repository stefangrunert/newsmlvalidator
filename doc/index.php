<?php
require dirname(__FILE__) . "/../lib/vendor/parsedown/Parsedown.php";
$parsedown = new Parsedown();
if (!isset($_GET['file'])) {
    $md = file_get_contents(dirname(__FILE__) . "/Embed_HTML5_and_Microdata_Into_NewsNL-G2.html");
} else {
    $md = file_get_contents(dirname(__FILE__) . "/" . $_GET['file']);
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title><?= isset($_GET['file']) ? $_GET['file'] : '' ?></title>
    <meta charset="utf-8"/>
    <link href="../lib/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="bower_components/jquery-ui/themes/smoothness/jquery-ui.min.css"/>
    <link rel="stylesheet" href="../css/styles.css"/>
    <link rel="stylesheet" href="../css/highlightjs/default.css"/>
    <script src="bower_components/bootstrap/dist/js/bootstrap.js"></script>
    <script src="../lib/highlight.pack.js"></script>
    <script>hljs.initHighlightingOnLoad();</script>
</head>
<body>
<?= $parsedown->text($md); ?>
</body>
</html>

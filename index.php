<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require dirname(__FILE__) . "/validator.php";
    exit;
};
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>NewsML-G2 Validator</title>
    <meta charset="utf-8"/>
    <link href="lib/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="css/styles.css"/>
    <script src="lib/vendor/jquery/jquery.min.js"></script>
    <script src="lib/vendor/bootstrap/js/bootstrap.js"></script>
    <script src="lib/vendor/angular/angular.min.js"></script>
    <script src="lib/newsmlvalidator.js"></script>
</head>
<body data-ng-app="nmlv" data-ng-controller="nmlvCtrl">

<h1 class="main-headline leftElement"><b>NewsML-G2 Validator</b> <span> + (</span> HTML 5 polyglot <span> + </span> Microdata <span> ) || </span> XHTML1.0
</h1>

<div id="validationForm" class="leftElement {{validationActive ? 'active' : 'inactive'}}">
<? include("lib/views/validation-form.html")?>
</div>

<br/>

<h4>Validation Results</h4>

<div id="validationResults" class="leftElement">
<? include("lib/views/validation-results.html")?>
</div>

<div id="info" class="rightElement">
<? include("lib/views/frontpage-aside.html")?>
</div>

</body>
</html>

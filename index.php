<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require dirname(__FILE__) . "/validator.php";
    exit;
};
?>
<!DOCTYPE html>
<html>
<head>
    <title>NewsML + XHTML5 + Microdata Validator</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <link href="bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="bower_components/jquery-ui/themes/smoothness/jquery-ui.min.css"/>
    <link rel="stylesheet" href="css/styles.css"/>
    <script src="bower_components/jquery/dist/jquery.min.js"></script>
    <script src="bower_components/jquery-ui/ui/minified/jquery-ui.min.js"></script>
    <script src="bower_components/bootstrap/dist/js/bootstrap.js"></script>
    <script src="bower_components/angular/angular.min.js"></script>
    <script src="lib/newsmlvalidator.js"></script>
</head>
<body ng-app="nmlv" ng-controller="nmlvCtrl">

<h1 class="leftElement"><b>3-Step</b> NewsML-G2 + XHTML5 + Microdata Validator</h1>

<div id="intro" class="leftElement">
    <b>
        <a href="" ng-click="loadExample('01', true)">load valid NewsML-G2 example</a> or
        <a href="" ng-click="loadExample('01', false)">load invalid NewsML-G2 example</a>
    </b>
    <br/>
    ...or paste NewsML-G2 document, containing XHTML5+Microdata within the contentSet/inlineXML into the form below:
</div>

<div class="leftElement">
    <textarea ng-model="payload" id="newsmlPayload" class="{{validationActive ? 'active' : 'inactive'}}"></textarea>
    <br/>
    <button ng-click="validate()" id="submitValidation" class="{{!payload ? 'disabled':''}}">Validate Document</button>
    <button ng-if="payload" ng-click="clear()" id="clearForm">Clear Form</button>
</div>

<br/>
<h4>Validation Results</h4>

<div ng-repeat="validation in validations">
    <div class="validationResult {{validation.active ? 'active' : 'inactive'}} leftElement">
        <div
            class="resicon {{validation.res.passed === true? 'passed':validation.res.passed === false? 'failed':'hidden'}}"></div>
        <div class="loader {{validation.loader === true ? 'active' : 'hidden'}}"></div>
        <h4>{{validation.name}}</h4>

        <div ng-repeat="validationResult in validation.res.validationResults">
            <span ng-if="validationResult.guid">processed item <b>{{validationResult.guid}}</b>:</span>

            <div class="status {{validationResult.passed ? 'valid' : 'invalid'}}">
                passed: {{validationResult.passed}}
            </div>
            <div class="message">
                {{validationResult.message}}
            </div>
        </div>
        <div class="validationInfo">
            <div ng-if="validation.name == 'NewsML'">
                NewsML-G2 validation uses
                <a href="http://dev.iptc.org/G2-Standards" class="disabled" target="_blank">IPTC XSD schema</a>
            </div>
            <div ng-if="validation.name == 'HTML'">
                HTML validation provided by <a href="https://validator.nu" target="_blank">https://validator.nu</a>
            </div>
            <div ng-if="validation.name == 'Microdata'">
                Microdata validation provided by
                <a href="http://linter.structured-data.org" target="_blank">http://linter.structured-data.org</a>
            </div>
        </div>
    </div>
</div>

<div id="info" class="rightElement">
    <p>
        <b>Disclaimer:</b>
        <i>
            The purpose of this project is showing how validating
            (X)HTML5 + Microdata documents embedded in NewsML-G2 can be done.
            <br/>
            Be aware, the implementation is very minimalistic. There is currently nothing like error handling
            or even QA.
        </i>
    </p>

    <p>
        <b>How it works:</b> The validation is performed in three independent steps:
    </p>
    <ol>
        <li>NewsML-G2 validation based on the XSD provided by IPTC.</li>
        <li>
            HTML validation of the inlineXML embedded HTML document withing the NewsML-G2 contentSet,
            using the API of <a href="https://validator.nu" target="_blank">https://validator.nu</a>
        </li>
        <li>Validation of microdata, embedded in the HTML document, (ab)using
            <a href="http://linter.structured-data.org" target="_blank">http://linter.structured-data.org</a>
        </li>
    </ol>
    <p>
        <b>Validation API:</b> Validation without using the graphical interface can be done by sending a POST request,
        containing the NewsML-G2 document in the POST body to the same URL as this page.
    </p>

    <p><b>Alternative validation services:</b> You can choose between a couple of services to validate HTML5 and
        Microdata.
        Please remember to add the right doctype definition to your XHTML document to make the validator recognize
        this is a polyglot HTML5 document (&lt;!DOCTYPE html&gt;)
    <ul>
        <li>
            <b>HTML5 validation</b>
            <a href="http://validator.w3.org/" target="_blank">The W3C Markup Validation Service</a>,
            <a href="https://validator.nu" target="_blank">validator.nu</a>
        </li>
        <li>
            <b>Microdata validation</b>
            <a href="https://developers.google.com/structured-data/testing-tool/" target="_blank">Google Testing
                Tool</a>,
            <a href="https://webmaster.yandex.com/microtest.xml" target="_blank">Yandex Structured Data Validator</a>,
            <a href="http://www.bing.com/toolbox/markup-validator" target="_blank">Bing Markup Validator (requires
                login)</a>
        </li>
    </ul>
    <p>
        <b>Want to Contribute?</b>
        Feel free to checkout the project from GitHub, improve it and send me a pull request.
    </p>

    <p><i>&copy; Stefan Grunert, 2015</i></p>
</div>

</body>
</html>



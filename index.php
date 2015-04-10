<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require dirname(__FILE__) . "/validator.php";
    exit;
};
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>NewsML + Polyglot HTML5 + Microdata Validator</title>
    <meta charset="utf-8"/>
    <link href="lib/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="css/styles.css"/>
    <script src="lib/vendor/jquery/jquery.min.js"></script>
    <script src="lib/vendor/bootstrap/js/bootstrap.js"></script>
    <script src="lib/vendor/angular/angular.min.js"></script>
    <script src="lib/newsmlvalidator.js"></script>
</head>
<body data-ng-app="nmlv" data-ng-controller="nmlvCtrl">

<h1 class="main-headline leftElement"><b>3-Step Validator</b> NewsML-G2 <span>-&gt;</span> Polyglot HTML5 <span>-&gt;</span> Microdata
</h1>

<div id="intro" class="leftElement">
    <b>
        <a href="" data-ng-click="loadExample('01', true)">load valid NewsML-G2 example</a> or
        <a href="" data-ng-click="loadExample('01', false)">load invalid NewsML-G2 example</a>
    </b>
    <br/>
    ...or paste NewsML-G2 document, containing polyglot HTML5 + Microdata within the contentSet/inlineXML into the form
    below:
</div>

<div class="leftElement">
    <textarea data-ng-model="payload" id="newsmlPayload" class="{{validationActive ? 'active' : 'inactive'}}"></textarea>
    <br/>
    <button data-ng-click="validate()" id="submitValidation" class="{{!payload ? 'disabled':''}}">Validate Document</button>
    <button data-ng-if="payload" data-ng-click="clear()" id="clearForm">Clear Form</button>
</div>

<br/>

<h4>Validation Results</h4>

<div data-ng-repeat="validation in validations">
    <div class="validationResult {{validation.active ? 'active' : 'inactive'}} leftElement">
        <div
            class="resicon {{validation.res.passed === true? 'passed':validation.res.passed === false? 'failed':'hidden'}}"></div>
        <div class="loader {{validation.loader === true ? 'active' : 'hidden'}}"></div>
        <h4>{{validation.name}}</h4>
        <div class="validationDetails {{validation.id}}">
            <div data-ng-repeat="validationResult in validation.res.validationResults">
                <span data-ng-if="validationResult.guid">processed item <b>{{validationResult.guid}}</b>:</span>

                <div class="status {{validationResult.passed ? 'valid' : 'invalid'}}">
                    passed: {{validationResult.passed}}
                </div>
                <div class="message">
                    {{validationResult.message}}
                </div>

            </div>
        </div>
        <div class="validationInfo">
            <div data-ng-if="validation.name == 'NewsML'">
                NewsML-G2 validation is using
                <a href="http://dev.iptc.org/G2-Standards" class="disabled" target="_blank">IPTC XSD schema</a>
            </div>
            <div data-ng-if="validation.name == 'Polyglot HTML5'">
                HTML validation provided by <a href="https://validator.nu" target="_blank">https://validator.nu</a>
            </div>
            <div data-ng-if="validation.name == 'Microdata'">
                Microdata validation provided by
                <a href="http://linter.structured-data.org" target="_blank">http://linter.structured-data.org</a>
            </div>
        </div>
    </div>
</div>

<div id="info" class="rightElement">
    <p><b>Introduction:</b> <a href="doc/Embed_HTML5_and_Microdata_Into_NewsNL-G2.html">
            Why using HTML5 polyglot + schema.org as the content format in NewsML-G2 is a good choice
        </a>
    </p>
    <p>
        <b>How it works:</b> The validation is performed in three independent steps:
    </p>
    <ol>
        <li>NewsML-G2 validation based on the XSD provided by <a href="http://iptc.org/" target="_blank">IPTC</a>.</li>
        <li>
            <a href="http://www.w3.org/TR/html-polyglot/" target="_blank">Polyglot HTML5</a> validation of the inlineXML
            embedded HTML document withing the NewsML-G2 contentSet,
            using the API of <a href="https://validator.nu" target="_blank">https://validator.nu</a>
        </li>
        <li>Validation of microdata, embedded in the HTML document, (ab)using
            <a href="http://linter.structured-data.org" target="_blank">http://linter.structured-data.org</a>
        </li>
    </ol>
    <p>
        <b>Validation API:</b> Validation without using the graphical interface can be done by sending a POST request,
        containing the NewsML-G2 document in the POST body to the same URL as this page.
        <br/>
        The API responds with HTTP status code 200 for valid documents, and 400 for invalid ones. The response body
        contains parsable JSON serialized info/error messages
    </p>

    <p><b>Alternative validation services:</b> You can choose between a couple of services to validate HTML5 and
        Microdata.
        Please remember to add the right doctype definition to your XHTML document to make the validator recognize
        this is a polyglot HTML5 document (&lt;!DOCTYPE html&gt;)
    </p>
    <ul>
        <li>
            <b>Polyglot HTML5 validation</b>
            <a href="http://validator.w3.org/" target="_blank">The W3C Markup Validation Service</a>,
            <a href="https://validator.nu" target="_blank">validator.nu</a>
        </li>
        <li>
            <b>Microdata validation</b>
            <a href="https://developers.google.com/structured-data/testidata-ng-tool/" target="_blank">Google Testing
                Tool</a>,
            <a href="https://webmaster.yandex.com/microtest.xml" target="_blank">Yandex Structured Data Validator</a>,
            <a href="http://www.bing.com/toolbox/markup-validator" target="_blank">Bing Markup Validator (requires
                login)</a>
        </li>
    </ul>

    <p>
        <b>Want to Contribute?</b>
        Feel free to checkout the project from <a href="https://github.com/arasix/newsmlvalidator" target="_blank">GitHub</a>,
        improve it and send me a pull request.
    </p>

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

    <p><i>© <a href="mailto:stefan@aptoma.com">Stefan Grunert, 2015</a></i></p>
</div>

</body>
</html>

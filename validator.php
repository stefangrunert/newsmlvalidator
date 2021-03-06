<?php
require "lib/classes/NewsMLValidator.php";

// preparing processing parameters
$standards = NewsMLValidator::getStandardsFromHTTPRequestParameter(
    isset($_REQUEST['standard']) ? $_REQUEST['standard'] : null
);
$isAPIRequest = !isset($_GET['appRequest']);
$contentType = NewsMLValidator::getRequestedFormat();

// getting the NewsML document from the request
$payload = trim(file_get_contents("php://input"));

// running the validation
$newsMLValidator = new NewsMLValidator();
$validations = $newsMLValidator->run($payload, $standards);

// assembling the output
$hasErrors = false;
$numErrors = 0;
$hasStandardElements = false;
foreach ($validations as $validation) {
    if ($validation->hasStandardElements) {
        $hasStandardElements = true;
    }
    if ($validation->hasError) {
        $hasErrors = true;
        $numErrors += $validation->numErrors;
    }
}
$response = new stdClass();
$response->passed = !$hasStandardElements ? null : $numErrors === 0;
$response->numErrors = $numErrors;
$response->validationResults = $validations;

// set response headers
$statusCode = $numErrors > 0 && $isAPIRequest ? 400 : 200;
header('HTTP/1.1: ' . $statusCode);
header('Content-type: ' . $contentType);

// output the validation result
die($contentType == 'application/json' ? json_encode($response) : XMLSerializer::generateValidXmlFromObj($response));

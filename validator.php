<?php
require "lib/classes/NewsMLValidator.php";

// preparing processing parameters
$standards = NewsMLValidator::getStandardsFromHTTPRequestParameter(
    isset($_REQUEST['standard']) ? $_REQUEST['standard'] : null
);
$isAPIRequest = !isset($_GET['appRequest']);

// getting the NewsML document from the request
$payload = trim(file_get_contents("php://input"));

// running the validation
$newsMLValidator = new NewsMLValidator();
$validations = $newsMLValidator->run($payload, $standards);

// assembling the output
$hasErrors = false;
$numErrors = 0;
foreach ($validations as $validation) {
    if ($validation->hasError) {
        $hasErrors = true;
        $numErrors += $validation->numErrors;
    }
}
$response = array();
$response['passed'] = $numErrors === 0;
$response['numErrors'] = $numErrors;
$response['validationResults'] = $validations;

// set response headers
http_response_code($numErrors > 0 && $isAPIRequest ? 400 : 200);
header('Content-type: application/json');

// output the validation result
die(json_encode($response));
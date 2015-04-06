<?php
/**
 * Disclaimer: The only purpose of this project is to give a proof of concept about the possibility to validate.
 * (X)HTML5 + Microdata documents embedded in NewsML-G2
 *
 * @author Stefan Grunert, stefan@aptoma.com
 */

require "lib/NewsMLValidator.php";

// getting the NewsML document from the request
$payload = trim(file_get_contents("php://input"));
$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : null;


// running the validation
$newsMLValidator = new NewsMLValidator();
$validations = $newsMLValidator->run($payload, $type);

// assembling the output
$validationResults = array();
$hasErrors = false;
$numErrors = 0;
foreach ($validations as $validation) {
    $validationResult = array();
    $validationResult['validationType'] = $validation->validatedPart;
    $validationResult['guid'] = $validation->guid;
    if ($validation->hasError) {
        $hasErrors = true;
        $numErrors++;
        $validationResult['passed'] = false;
        $validationResult['message'] = $validation->errorMsg;
    } else {
        $validationResult['passed'] = true;
    }
    $validationResults[] = $validationResult;
}
$response = array();

if ($hasErrors) {
    //http_response_code(400);
    $response['passed'] = false;
} else {
    //http_response_code(200);
    $response['passed'] = true;
}
$response['numErrors'] = $numErrors;
$response['validationResults'] = $validationResults;

// outputting the validation result
header('Content-type: application/json');
die(json_encode($response));

<?php

class MicrodataValidationRunner
{
    /**
     * Validate microdata "ab"-using linter.structured-data.org (they don't provide an API)
     *
     * @param string $html HTML document
     * @return NewsMLValidationResult
     */
    public function run($html, $guid)
    {
        $mdLinterPayload = json_encode(array("content" => $html));
        $mdLinterUrl = "http://linter.structured-data.org";
        $mdLint =CurlService::curl($mdLinterUrl, 'POST', $mdLinterPayload, "application/json");
        $mdLintObject = json_decode($mdLint['body']);
        $newsMLValidation = new NewsMLValidationResult('Microdata');
        $newsMLValidation->guid = $guid;
        $numErrors = 0;
        if (!empty ($mdLintObject->messages)) {
            $newsMLValidation->hasError = true;
            $newsMLValidation->message = join(', ', $mdLintObject->messages);
            $numErrors = count($mdLintObject->messages);
        }
        $newsMLValidation->passed = $numErrors === 0;
        $newsMLValidation->numErrors = $numErrors;
        //$newsMLValidation->responseBody = $mdLint['body'];
        return $newsMLValidation;
    }
}
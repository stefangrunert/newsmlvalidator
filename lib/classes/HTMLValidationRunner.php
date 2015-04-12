<?php

class HTMLValidationRunner
{
    /**
     * Validates HTML documents using validator.nu's API
     *
     * @param string $html HTML document
     * @return NewsMLValidationResult
     */
    public function run($html, $guid)
    {
        $newsMLValidation = new NewsMLValidationResult('XHTML');
        $newsMLValidation->guid = $guid;
        $docProps = DocumentDetector::detectHTML($html);
        $newsMLValidation->detections = $docProps;
        if (! DocumentDetector::isSupportedHTMLStandard($docProps->standard)) {
            $newsMLValidation->hasError = true;
            $newsMLValidation->message = "HTML document type not supported or not correctly detected";
            return $newsMLValidation;
        }
        $validatorUrl = "https://validator.nu/?out=json";
        $validatorUrl .= "&preset=" . urlencode(DocumentDetector::validateNuPreset($docProps->standard));
        $html = "<!DOCTYPE " . DocumentDetector::doctypeDeclaration($docProps->standard) . ">" . $html;
        $htmlLint = CurlService::curl($validatorUrl, 'POST', $html, "application/xhtml+xml");
        $validatorObject = json_decode($htmlLint['body']);
        $numErrors = 0;
        if (!empty ($validatorObject->messages)) {
            foreach ($validatorObject->messages as $message) {
                if ($message->type == "error") {
                    $error = new stdClass();
                    $error->message = $message->message;
                    $error->line = $message->lastLine;
                    $error->column = $message->firstColumn;
                    $error->markup = $message->extract;
                    $newsMLValidation->errors[] = $error;
                    $numErrors ++;
                }
            }
        }
        $newsMLValidation->numErrors = $numErrors;
        $newsMLValidation->hasError = $numErrors > 0;
        $newsMLValidation->passed = $numErrors === 0;
        $newsMLValidation->message = NewsMLValidationResult::generateMessage($numErrors);
        $newsMLValidation->service = "validator.nu";
        return $newsMLValidation;
    }

}
<?php

class MicrodataValidationRunner
{
    /**
     * Validate microdata "ab"-using linter.structured-data.org (they don't provide an API)
     *
     * @param string $html HTML document
     * @return NewsMLValidationResult
     */

    public function run(DOMElement $newsItem, $guid)
    {
        $htmlElement = HTMLValidationRunner::getContentHTML($newsItem);
        $newsMLValidation = new MicrodataValidationResult('Microdata');
        $newsMLValidation->guid = $guid;

        if (! $htmlElement instanceof DOMElement) {
            $newsMLValidation->hasStandardElements = false;
            $newsMLValidation->message = "No HTML content element detected in NewsItem.";
            return $newsMLValidation;
        }
        $html = $htmlElement->ownerDocument->saveXML($htmlElement);
        $newsMLValidation->documentOffsetLine = $htmlElement->getLineNo() - 2;

        if (!$this->containsMicrodata($html)) {
            $newsMLValidation->hasStandardElements = false;
            $newsMLValidation->message = "No microdata detected within the HTML document.";
            return $newsMLValidation;
        } else {
            $newsMLValidation->hasStandardElements = true;
        }

        $docProps = DocumentDetector::detectHTML($html);
        $newsMLValidation->detections = $docProps;
        $html = "<!DOCTYPE " . DocumentDetector::doctypeDeclaration($docProps->standard) . ">" . $html;
        try {
            return $this->runGoogleTestingTool($html, $newsMLValidation);
        } catch (Exception $e) {
            // fallback when Google validation fails
            return $this->runLinterStructuredData($html, $newsMLValidation);
        }
    }

    private function containsMicrodata($html)
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->loadXML($html);
        $xp = new DOMXPath($dom);
        $q ="//*/@itemscope | //*/@itemprop | //*/@itemtype";
        return $xp->query($q)->length > 0;
    }

    public function runLinterStructuredData($html, $newsMLValidation)
    {
        $mdLinterPayload = json_encode(array("content" => $html));
        $mdLinterUrl = "http://linter.structured-data.org";
        $mdLint = CurlService::curl($mdLinterUrl, 'POST', $mdLinterPayload, "application/json");
        $mdLintObject = json_decode($mdLint['body']);
        $numErrors = 0;
        if (!empty ($mdLintObject->messages)) {
            foreach ($mdLintObject->messages as $lintError) {
                $numErrors++;
                $error = new stdClass();
                $error->message = $lintError;
                $error->line = 'n.a.';
                $error->column = 'n.a.';
                $error->markup = 'n.a.';
                $newsMLValidation->errors[] = $error;
            }
        }
        $newsMLValidation->passed = $numErrors === 0;
        $newsMLValidation->numErrors = $numErrors;
        if ($numErrors > 0) {
            $newsMLValidation->hasError = true;
            $m = $numErrors . " error";
            if ($numErrors > 1) {
                $m .= "s";
            }
            $m .= " detected";
            $newsMLValidation->message = $m;
        }
        $newsMLValidation->service = "Structured Data Linter";
        return $newsMLValidation;
    }

    public function runGoogleTestingTool($html, $newsMLValidation)
    {
        $payload = "html=" . urlencode($html);
        $url = "https://structured-data-testing-tool.developers.google.com/sdtt/web/validate";
        $googleVal = CurlService::curl($url, 'POST', $payload, "application/x-www-form-urlencoded;charset=utf-8");
        $responseBody = preg_replace("#^(\)\]\}\'\s)#", '', $googleVal['body']);
        if (empty($responseBody)) {
            throw new Exception("Empty response");
        }
        $result = json_decode($responseBody);
        if (empty($result)) {
            throw new Exception("JSON error");
        }
        $newsMLValidation->inspections = $result->tripleGroups;
        $numErrors = 0;
        if (!empty ($result->errors)) {
            foreach ($result->errors as $ve) {
                $numErrors++;
                $error = new stdClass();
                $path = ";";
                if (!empty($ve->args)) {
                    rsort($ve->args);
                    $path = join('/', $ve->args);
                }
                $error->message = $path . ': ' . $ve->errorType;
                $line =  substr_count(substr($html, 0, $ve->begin), "\n") + 1;
                $error->line = $line;
                $error->column = $ve->begin . ' - ' . $ve->end;
                $error->markup = mb_substr($html, $ve->begin, $ve->end - $ve->begin);
                $newsMLValidation->errors[] = $error;
            }
        }
        $newsMLValidation->passed = $numErrors === 0;
        $newsMLValidation->numErrors = $numErrors;
        $newsMLValidation->hasError = $numErrors > 0;
        $newsMLValidation->message = NewsMLValidationResult::generateMessage($numErrors);
        $newsMLValidation->service = "Google Structured Data Testing Tool";
        return $newsMLValidation;
    }


}
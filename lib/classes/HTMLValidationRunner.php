<?php

class HTMLValidationRunner
{

    public function run(DOMElement $newsItem, $guid)
        {
            $htmlElement = self::getContentHTML($newsItem);
            $newsMLValidation = new NewsMLValidationResult('XHTML');
            $newsMLValidation->guid = $guid;
            if (!$htmlElement instanceof DOMElement) {
                $newsMLValidation->hasStandardElements = false;
                $newsMLValidation->message = "No HTML content element detected in NewsItem.";
                return $newsMLValidation;
            } else {
                $newsMLValidation->hasStandardElements = true;
            }
            $html = $htmlElement->ownerDocument->saveXML($htmlElement);
            $newsMLValidation->documentOffsetLine = $htmlElement->getLineNo() - 2;
            $docProps = DocumentDetector::detectHTML($html);
            $newsMLValidation->detections = $docProps;
            if (! DocumentDetector::isSupportedHTMLStandard($docProps->standard)) {
                $newsMLValidation->hasError = true;
                $newsMLValidation->message = "HTML document type not supported or not correctly detected";
                return $newsMLValidation;
            }
            $schema = $this->loadXHTML5Schema($docProps);
            $dom = DocumentDetector::loadXHTMLDom($html, $docProps);
            libxml_use_internal_errors(true);
            $res = $dom->schemaValidateSource($schema);
            $numErrors = 0;
            if ($res == false) {
                $newsMLValidation->hasError = true;
                $fileA = mbsplit("\n", $html);
                $errors = libxml_get_errors();
                $numErrors = count($errors);
                foreach ($errors as $error) {
                    $error->markup = (isset($fileA[$error->line - 1]) ? trim($fileA[$error->line - 1]) : '');
                    $newsMLValidation->errors[] = $error;
                }
            }
            $newsMLValidation->passed = $numErrors === 0;
            $newsMLValidation->numErrors = $numErrors;
            $newsMLValidation->hasError = $numErrors > 0;
            $newsMLValidation->message = NewsMLValidationResult::generateMessage($numErrors);
            $newsMLValidation->service = "XML schema validation";
            return $newsMLValidation;
        }

        private function loadXHTML5Schema(DocumentProperties $docProps)
        {

            $dir = dirname(__FILE__) . "/../xsd/xhtml";
            if ($docProps->standard == "HTML5") {
                $file = "5/xhtml5_nmlg2e.xsd";
            } else {
                $file = "1.0/xhtml1-strict.xsd";
            }
           return file_get_contents($dir . "/" . $file);
        }


    /**
     * Validates HTML documents using validator.nu's API
     *
     * @param string $html HTML document
     * @return NewsMLValidationResult
     */
    public function runService(DOMElement $newsItem, $guid)
    {
        $htmlElement = self::getContentHTML($newsItem);
        $newsMLValidation = new NewsMLValidationResult('XHTML');
        $newsMLValidation->guid = $guid;
        if (!$htmlElement instanceof DOMElement) {
            $newsMLValidation->hasStandardElements = false;
            $newsMLValidation->message = "No HTML content element detected in NewsItem.";
            return $newsMLValidation;
        } else {
            $newsMLValidation->hasStandardElements = true;
        }
        $html = $htmlElement->ownerDocument->saveXML($htmlElement);
        $newsMLValidation->documentOffsetLine = $htmlElement->getLineNo() - 2;
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

    public static function getContentHTML(DOMElement $newsItem)
    {
        $dom = $newsItem->ownerDocument;
        $htmlElement = NewsMLValidator::getNewsMLXpath($dom)->query(
            'descendant::n:inlineXML[@contenttype="application/xhtml+xml" ' .
            'or @contenttype="application/xhtml+html"]/h:html',
            $newsItem
        )->item(0);
        if (! $htmlElement instanceof DOMElement) {
            return null;
        }
        return $htmlElement;
    }
}

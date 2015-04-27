<?php


class NITFValidationRunner
{
    public function run(DOMElement $newsItem, $guid)
    {
        $newsMLValidation = new NewsMLValidationResult('NITF');
        $newsMLValidation->documentOffsetLine = $newsItem->getLineNo();
        $newsMLValidation->guid = $guid;
        $nitf = self::getContentNITF($newsItem);
        if (empty($nitf)) {
            $newsMLValidation->hasStandardElements = false;
            $newsMLValidation->message = "No NITF content element detected in NewsItem.";
            return $newsMLValidation;
        } else {
            $newsMLValidation->hasStandardElements = true;
        }
        $props = DocumentDetector::detectNITF($nitf);
        $dom = DocumentDetector::loadNITFDom($nitf);
        $schema = $this->loadNITFSchema($props);
        libxml_use_internal_errors(true);
        $res = $dom->schemaValidateSource($schema);
        $numErrors = 0;
        if ($res == false) {
            $newsMLValidation->hasError = true;
            $fileA = mbsplit("\n", $dom->saveXML());
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
        $newsMLValidation->service = "XSD schema by IPTC";
        $newsMLValidation->detections = $props;
        return $newsMLValidation;
    }


    private function loadNITFSchema(DocumentProperties $props)
    {
        $dirname = dirname(__FILE__) . "/../xsd/nitf/";
        $filename = $props->validationSchema;
        if (file_exists($dirname . "/" . $filename)) {
            return file_get_contents($dirname . "/" . $filename);
        } else {
            $props->validationSchema = "nitf-3.6.xsd";
            $props->versionMismatch = true;
            return file_get_contents($dirname . "/nitf-3.6.xsd");
        }
    }

    public static function getContentNITF(DOMElement $newsItem)
    {
        $dom = $newsItem->ownerDocument;
        $nitfElement = NewsMLValidator::getNewsMLXpath($dom)->query(
            'descendant::n:inlineXML[@contenttype="application/nitf+xml"]/nitf:nitf',
            $newsItem
        )->item(0);
        if (! $nitfElement instanceof DOMElement) {
            return null;
        }
        return $dom->saveXML($nitfElement);
    }



}
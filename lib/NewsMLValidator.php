<?php
require "NewsMLValidationResult.php";

/**
 * Class NewsMLValidator
 *
 *  Validates NewsML-G2 with XHTML5 + Microdata in three steps.
 *
 */
class NewsMLValidator
{
    /**
     * @param string $newsML NewsML-G2 document
     * @param string|null $validationRequest
     * @return array
     */
    public function run($newsML, $validationRequest = null)
    {
        $validations = array();
        if (empty($validationRequest) || $validationRequest == 'NewsML') {
            $validations[] = $this->validateNewsML($newsML);
        }
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->loadXML($newsML);
        $xp = new DOMXPath($dom);
        $xp->registerNamespace('n', 'http://iptc.org/std/nar/2006-10-01/');
        $xp->registerNamespace('h', 'http://www.w3.org/1999/xhtml');
        $newsItems = $xp->query('//n:newsItem');
        if (empty($validationRequest) || $validationRequest == 'HTML') {
            foreach ($newsItems as $newsItem) {
                $guid = $newsItem->getAttribute('guid');
                $htmlElement = $xp->query('descendant::h:html', $newsItem)->item(0);
                $htmlCode = '<!DOCTYPE html>' . $dom->saveXML($htmlElement);
                $validations[] = $this->validateHTML($htmlCode, $guid);
            }
        }
        if (empty($validationRequest) || $validationRequest == 'Microdata') {
            foreach ($newsItems as $newsItem) {
                $guid = $newsItem->getAttribute('guid');
                $htmlElement = $xp->query('descendant::h:html', $newsItem)->item(0);
                $htmlCode = '<!DOCTYPE html>' . $dom->saveXML($htmlElement);
                $validations[] = $this->validateMicrodata($htmlCode, $guid);
            }
        }
        return $validations;
    }

    /**
     * Validates NewsML-G2 against XSD schema
     *
     * @param string $newsML NewsML-G2 document
     * @return NewsMLValidationResult
     */
    private function validateNewsML($newsML)
    {
        $schema = file_get_contents(dirname(__FILE__) . "/../xsd/NewsML-G2_2.12-spec-All-Power.xsd");
        $newsMLValidation = new NewsMLValidationResult('NewsML-G2');
        libxml_use_internal_errors(true);
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->loadXML($newsML);
        $res = $dom->schemaValidateSource($schema);

        if ($res == false) {
            $newsMLValidation->hasError = true;
            $fileA = mbsplit("\n", $newsML);
            $errors = libxml_get_errors();
            $numErrors = count($errors);
            foreach($errors as $error) {
                $error->markup = (isset($fileA[$error->line - 1]) ? trim($fileA[$error->line - 1]) : '');
                $newsMLValidation->errors[] = $error;
            }
            $m = $numErrors . " error";
            if ($numErrors > 1) {
                $m .= "s";
            }
            $m .= " detected";
            $newsMLValidation->errorMsg = $m;
        }
        return $newsMLValidation;
    }

    /**
     * Validates HTML documents using validator.nu's API
     *
     * @param string $html HTML document
     * @return NewsMLValidationResult
     */
    private function validateHTML($html, $guid)
    {
        $validatorUrl = "https://validator.nu/?out=json";
        $htmlLint = $this->curl($validatorUrl, 'POST', $html, "text/html");
        $validatorObject = json_decode($htmlLint['body']);
        $newsMLValidation = new NewsMLValidationResult('XHTML5');
        $newsMLValidation->guid = $guid;
        if (!empty ($validatorObject->messages)) {
            foreach ($validatorObject->messages as $message) {
                if ($message->type == "error") {
                    $newsMLValidation->hasError = true;
                    $newsMLValidation->errorMsg = $message->message;
                }
            }
        }
        $newsMLValidation->responseBody = $htmlLint['body'];
        return $newsMLValidation;
    }

    /**
     * Validate microdata "mis"-using linter.structured-data.org
     *
     * @param string $html HTML document
     * @return NewsMLValidationResult
     */
    private function validateMicrodata($html, $guid)
    {
        $mdLinterPayload = json_encode(array("content" => $html));
        $mdLinterUrl = "http://linter.structured-data.org";
        $mdLint = $this->curl($mdLinterUrl, 'POST', $mdLinterPayload, "application/json");
        $mdLintObject = json_decode($mdLint['body']);
        $newsMLValidation = new NewsMLValidationResult('Microdata');
        $newsMLValidation->guid = $guid;
        if (!empty ($mdLintObject->messages)) {
            $newsMLValidation->hasError = true;
            $newsMLValidation->errorMsg = join(', ', $mdLintObject->messages);
        }
        $newsMLValidation->responseBody = $mdLint['body'];
        return $newsMLValidation;
    }

    private function curl($url, $method = 'GET', $data = null, $contentType = 'text/plain')
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_COOKIESESSION, false);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            if (!empty($data)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: ' . $contentType));
        } elseif ($method == 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }
        $res = curl_exec($ch);
        $info = curl_getinfo($ch);
        $header = substr($res, 0, $info['header_size']);
        $headers = array();
        $body = substr($res, $info['header_size']);
        $hs = mb_split("\n", $header);
        foreach ($hs as $h) {
            $hed = explode(':', $h, 2);
            if (isset($hed[1])) {
                $headers[$hed[0]] = trim($hed[1]);
            }
        }
        $info['headers'] = $headers;
        curl_close($ch);
        return array('body' => $body, 'info' => $info);
    }
}

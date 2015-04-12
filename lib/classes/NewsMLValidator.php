<?php
require "DocumentDetector.php";
require "DocumentProperties.php";
require "NewsMLValidationResult.php";
require "NewsMLValidationRunner.php";
require "HTMLValidationRunner.php";
require "MicrodataValidationRunner.php";
require "CurlService.php";

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
    public static $supportedStandards = array('NewsML', 'HTML', 'Microdata');


    public function run($newsML, $standards)
    {
        $validations = array();

        // validate NewsML
        if (in_array('NewsML', $standards)) {
            $validationRunner = new NewsMLValidationRunner();
            $validations[] = $validationRunner->run($newsML);
        }

        // extract contained NewsItems
        $newsItems = $this->extractNewsItems($newsML);

        // validate HTML
        if (in_array('HTML', $standards)) {
            foreach ($newsItems as $newsItem) {
                $guid = $newsItem->getAttribute('guid');
                $validationRunner = new HTMLValidationRunner();
                $html = $this->getContentHTML($newsItem);
                $validations[] = $validationRunner->run($html, $guid);
            }
        }

        // validate Microdata
        if (in_array('Microdata', $standards)) {
            foreach ($newsItems as $newsItem) {
                $guid = $newsItem->getAttribute('guid');
                $html =  '<!DOCTYPE html>'. $this->getContentHTML($newsItem);
                $validationRunner = new MicrodataValidationRunner();
                $validations[] = $validationRunner->run($html, $guid);
            }
        }
        return $validations;
    }

    private function extractNewsItems($newsML)
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->loadXML($newsML);
        return $this->getNewsMLXpath($dom)->query('//n:newsItem');
    }

    private function getContentHTML(DOMElement $newsItem)
    {
        $dom = $newsItem->ownerDocument;
        $htmlElement = $this->getNewsMLXpath($dom)->query('descendant::h:html', $newsItem)->item(0);
        return $dom->saveXML($htmlElement);
    }

    private function getNewsMLXpath(DOMDocument $dom)
    {
        $xp = new DOMXPath($dom);
        $xp->registerNamespace('n', 'http://iptc.org/std/nar/2006-10-01/');
        $xp->registerNamespace('h', 'http://www.w3.org/1999/xhtml');
        return $xp;
    }

    public static function getStandardsFromHTTPRequestParameter($param)
    {
        if (empty($param)) {
            return self::$supportedStandards;
        }
        $split = explode(',', $param);
        $standards = array();
        foreach ($split as $standard) {
            if (in_array(trim($standard), self::$supportedStandards)) {
                $standards[] = trim($standard);
            }
        }
        return $standards;
    }
}

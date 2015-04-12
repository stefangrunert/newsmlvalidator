<?php

class DocumentDetector
{

    public static function detectNewsML($newsML)
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->loadXML($newsML);
        $xp = new DOMXPath($dom);
        $xp->registerNamespace('n', 'http://iptc.org/std/nar/2006-10-01/');
        $xp->registerNamespace('h', 'http://www.w3.org/1999/xhtml');
        $newsItem = $xp->query('//n:packageItem[1] | //n:newsItem[1] | //n:planningItem[1]')->item(0);
        if (!$newsItem instanceof DOMElement) {
            throw new Exception("Could not detect valid NewsML document: no item found in document");
        }
        $documentProperties = new DocumentProperties();
        $documentProperties->standard = self::getAttribute('standard', $xp, $newsItem);
        $documentProperties->version = self::getAttribute('standardversion', $xp, $newsItem);
        $documentProperties->conformance = self::getAttribute('conformance', $xp, $newsItem);
        $documentProperties->doctype = 'xml';
        $documentProperties->contentType = 'text/xml';
        return $documentProperties;
    }

    public static function detectHTML($html)
    {
        $doctype = self::detectHTMLDoctype($html);
        $documentProperties = new DocumentProperties();
        $documentProperties->standard = $doctype;
        //$documentProperties->standard = 'HTML5';
        //$documentProperties->version = 'strict';
        $documentProperties->version = $doctype == 'HTML5' ? 'polyglot (XHTML5)' : 'strict';
        $documentProperties->doctype = self::doctypeDeclaration($doctype);
        $documentProperties->contentType = 'application/xhtml+xml';
        $documentProperties->validationSchema = 'not available';
        return $documentProperties;
    }

    private static function detectHTMLDoctype($html)
    {

        if (self::isHTML5($html)) {
            return "HTML5";
        } else {
            return "XHTML1.0";
        }

    }

    private static function isHTML5($html) {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->loadXML($html);
        $xp = new DOMXPath($dom);
        $xp->registerNamespace('h', 'http://www.w3.org/1999/xhtml');
        $q = "//h:article|//h:figure|//h:video|//h:bdi|//h:details|//h:dialog|//h:figcaption|//h:footer" .
            "|//h:header|//h:main|//h:mark|//h:meuitem|//h:meter|//h:nav|//h:progress|//h:rp|//h:rt|//h:ruby" .
            "|//h:section|//h:summary|//h:time|//h:wbr";
        $html5Elements = $xp->query($q);
        if ($html5Elements->length > 0) {
            return true;
        }
        return false;
    }

    private static function getAttribute($attribute, $xpath, $contextNode)
    {
        $node = $xpath->query("@" . $attribute, $contextNode)->item(0);
        return $node instanceof DOMAttr ? $node->nodeValue : '';
    }

    public static function validateNuPreset($doctype)
    {
        $presets = array(
            'HTML5' => 'http://s.validator.nu/xhtml5.rnc http://s.validator.nu/html5/assertions.sch http://c.validator.nu/all/',
            'XHTML1.0' => 'http://s.validator.nu/xhtml1-ruby-rdf-svg-mathml.rnc http://s.validator.nu/html4/assertions.sch http://c.validator.nu/all-html4/'
        );
        return $presets[$doctype];
    }

    public static function doctypeDeclaration($doctype)
    {
        $defs = array(
            'HTML5' => 'html',
            'XHTML1.0' => 'html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"'
        );
        return $defs[$doctype];
    }

    public static function supportedHTMLStandards()
    {
        return array('HTML5', 'XHTML1.0');
    }

    public static function isSupportedHTMLStandard($standard)
    {
        return in_array($standard, self::supportedHTMLStandards());
    }


}

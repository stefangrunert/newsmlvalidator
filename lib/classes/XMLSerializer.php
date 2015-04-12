<?php

class XMLSerializer {

    // functions adopted from http://www.sean-barton.co.uk/2009/03/turning-an-array-or-object-into-xml-using-php/

    public static function generateValidXmlFromObj(stdClass $obj, $node_block='newsMLValidation', $node_name='node') {
        $arr = get_object_vars($obj);
        return self::generateValidXmlFromArray($arr, $node_block, $node_name);
    }

    public static function generateValidXmlFromArray($array, $node_block='nodes', $node_name='node') {
        $xml = '<?xml version="1.0" encoding="UTF-8" ?>';

        $xml .= '<' . $node_block . '>';
        $xml .= self::generateXmlFromArray($array, $node_name);
        $xml .= '</' . $node_block . '>';

        return $xml;
    }

    private static function generateXmlFromArray($array, $node_name) {
        $xml = '';

        if (is_array($array) || is_object($array)) {
            foreach ($array as $key=>$value) {
                if (is_numeric($key)) {
                    $key =  substr($node_name, 0, mb_strlen($node_name) - 1);
                    //$node_name = substr($node_name, 0 , 3);
                }

                $xml .= '<' . $key . '>' . self::generateXmlFromArray($value, $key) . '</' . $key . '>';
            }
        } else {
            if (is_bool($array)) {
                $xml = $array === true ? 'true' : 'false';
            } else {
                $xml = htmlspecialchars($array, ENT_QUOTES);
            }
        }

        return $xml;
    }

}
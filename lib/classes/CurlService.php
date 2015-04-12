<?php

class CurlService
{
    public static function curl($url, $method = 'GET', $data = null, $contentType = 'text/plain')
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
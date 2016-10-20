<?php

/**
 * CURL封装函数
 *
 * @param string $url 请求的url
 * @param array $postData post数据，空数组则为get请求
 * @param string $cookie
 * @param string $cookiejar
 * @param string $referer
 * @param int $header 是否显示header
 * @param int $timeout 超时时间
 * @return string 返回请求数据
 *
 * @Package zjdboyYC
 * @Support http://www.kaoyan.com
 * @Author  Yuelong <yuelonghu@100tal.com>
 * @version $Id$
 */
function vcurl($url, $postData = array(), $cookie = '', $cookiejar = '', $referer = '', $header = 0, $timeout = 5) {
    $info = '';
    $cookiepath = getcwd() . './' . $cookiejar;
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    if ($referer) {
        curl_setopt($curl, CURLOPT_REFERER, $referer);
    } else {
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
    }
    if ($postData) {
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
    }
    if ($cookie) {
        curl_setopt($curl, CURLOPT_COOKIE, $cookie);
    }
    if ($cookiejar) {
        curl_setopt($curl, CURLOPT_COOKIEJAR, $cookiepath);
        curl_setopt($curl, CURLOPT_COOKIEFILE, $cookiepath);
    }
    //curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_TIMEOUT, $timeout); //超时时间5秒
    curl_setopt($curl, CURLOPT_HEADER, $header); //此处为1可以使返回值加上header
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $info = curl_exec($curl);
    if (curl_errno($curl)) {
        //        echo 'curl error: ' . curl_error($curl);
        return false;
    }
    curl_close($curl);
    return $info;
}

?>
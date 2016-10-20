<?php

/**
 * YC Framework
 * [zjdboy!] (C)2006-2016 zjdboy Inc. 
 *
 * 获取当前客户端IP
 * 解析透明代理过来的IP 客户端IP
 *
 * @Package YC Framework
 * @Support http://www.yangguang.com
 * @Author  zjdboy <zjdboy@163.com>
 * @version $Id: yc.getip.php 3 2011-12-29 15:01:09Z zjdboy $
 **/
function getip() {
    $ip = '0.0.0.0';
    if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
        $ip = getenv('HTTP_CLIENT_IP');
    } elseif (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
        $ip = getenv('HTTP_X_FORWARDED_FOR');
    } elseif (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
        $ip = getenv('REMOTE_ADDR');
    } elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return ($ip);
}

?>
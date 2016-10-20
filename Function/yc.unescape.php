<?php

/**
 * YC Framework
 * [zjdboy!] (C)2006-2009 Eduu Inc. (http://www.eduu.com)
 *
 * Js的Escape函数编码的字符进行解码
 *
 * @Package YC Framework
 * @Support http://www.yangguang.com
 * @Author  zjdboy <zjdboy@163.com>
 * @version $Id: yc.unescape.php 3 2011-12-29 15:01:09Z zjdboy $
 **/
function unEscape($str) {
    $str = rawurldecode($str);
    preg_match_all('/%u.{4}|&#x.{4};|&#d+;|.+/U', $str, $r);
    $ar = $r[0];
    foreach ($ar as $k => $v) {
        if (substr($v, 0, 2) == '%u') {
            $ar[$k] = iconv('UCS-2', 'GBK', pack('H4', substr($v, - 4)));
        } elseif (substr($v, 0, 3) == '&#x') {
            $ar[$k] = iconv('UCS-2', 'GBK', pack('H4', substr($v, 3, - 1)));
        } elseif (substr($v, 0, 2) == '&#') {
            $ar[$k] = iconv('UCS-2', 'GBK', pack('n', substr($v, 2, - 1)));
        }
    }
    return join('', $ar);
}
?>
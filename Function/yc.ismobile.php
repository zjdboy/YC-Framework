<?php

/**
 * YC Framework
 * [zjdboy!] (C)2006-2009 Eduu Inc. (http://www.eduu.com)
 *
 * 检测是否来自移动端
 *
 * @Package YC Framework
 * @Author  zjdboy <zjdboy@163.com>
 * @version $Id$
 **/
function isMobile() {
    $type = 0; //0未知，1IOS，2安卓
    $_tmp = $_SERVER['HTTP_USER_AGENT'];
    if (false !== stripos($_tmp, 'android')) {
        $type = 2;
    } elseif (stripos($_tmp, 'iphone') || stripos($_tmp, 'ipod')) {
        $type = 1;
    } else {
        $type = 0;
    }
    return $type;
}
?>
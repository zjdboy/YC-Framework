<?php

/**
 * YC Framework
 * [zjdboy!] (C)2006-2009 Eduu Inc. (http://www.eduu.com)
 *
 * 检测一个IP地址是否正常
 * 只能进行模糊的检测
 *
 * @Package YC Framework
 * @Support http://www.yangguang.com
 * @Author  zjdboy <zjdboy@163.com>
 * @version $Id: yc.isip.php 3 2011-12-29 15:01:09Z zjdboy $
 **/
function isIp($ip) {
    return preg_match('/^\d{0,3}\.\d{0,3}\.\d{0,3}\.\d{0,3}$/', $ip) ? true : false;
}
?>
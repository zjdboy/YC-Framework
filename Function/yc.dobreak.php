<?php

/**
 * YC Framework
 * [zjdboy!] (C)2006-2009 zjdboy Inc. (http://YC.zjdboy.Net)
 *
 * 301重定向
 * 默认定向到来访页面
 *
 * @param string $url 转向的URL地址
 * @return void
 *
 * @Package YC Framework
 * @Support http://www.yangguang.com
 * @Author  zjdboy <zjdboy@163.com>
 * @version $Id: yc.dobreak.php 3 2011-12-29 15:01:09Z zjdboy $
 **/
function doBreak($url = null) {
    header('location:' . (! $url ? $_SERVER['HTTP_REFERER'] : $url));
    exit();
}
?>
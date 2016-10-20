<?php

/**
 * YC Framework
 * [zjdboy!] (C)2006-2009 zjdboy Inc. (http://YC.zjdboy.Net)
 *
 * 取得Microtime精确时间
 *
 * @return float
 * @--------------------------------
 * @Package YC Framework
 * @Support http://www.yangguang.com
 * @Author  zjdboy <zjdboy@163.com>
 * @version $Id: yc.mctime.php 3 2011-12-29 15:01:09Z zjdboy $
 **/
function mcTime() {
    list ($usec, $sec) = explode(' ', microtime());
    return ((float) $usec + (float) $sec);
}
?>
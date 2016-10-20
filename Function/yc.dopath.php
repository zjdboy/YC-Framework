<?php

/**
 * YC Framework
 * [zjdboy!] (C)2006-2009 zjdboy Inc. (http://YC.zjdboy.Net)
 *
 * 正确解析路径
 * 解析为符合当前系统的路径表示
 *
 * @param string $str 物理路径字符串
 * @return string 得到当前系统规范的物理路径
 * @--------------------------------
 * @Package zjdboyYC
 * @Support http://bbs.zjdboy.net
 * @Author  zjdboy <zjdboyhr@gmail.com>
 * @version $Id: yc.dopath.php 3 2011-12-29 15:01:09Z zjdboy $
 **/
function doPath($str) {
    $str = preg_replace('/[\/\\\\]+/', DIRECTORY_SEPARATOR, $str);
    substr($str, - 1) != DIRECTORY_SEPARATOR && $str .= DIRECTORY_SEPARATOR;
    return $str;
}
?>
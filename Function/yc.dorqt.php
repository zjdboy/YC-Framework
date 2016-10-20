<?php
/**
 * YC Framework
 * [zjdboy!] (C)2006-2009 zjdboy Inc. (http://YC.zjdboy.Net)
 *
 * 获取GET参数
 * 检测并增加addslashes
 *
 * @Package zjdboyYC
 * @Support http://bbs.zjdboy.net
 * @Author  zjdboy <zjdboyhr@gmail.com>
 * @version $Id: yc.doget.php 3 2011-12-29 14:52:17Z zjdboy $
 **/
if (get_magic_quotes_gpc()) {

    function dorqt($name) {
        if (isset($_REQUEST[$name])) {
            return is_array($_REQUEST[$name]) ? $_REQUEST[$name] : trim($_REQUEST[$name]);
        } else {
            return null;
        }
    }
} else {

    function dorqt($name) {
        if (isset($_REQUEST[$name])) {
            return is_array($_REQUEST[$name]) ? $_REQUEST[$name] : addslashes(trim($_REQUEST[$name]));
        } else {
            return null;
        }
    }
}
?>
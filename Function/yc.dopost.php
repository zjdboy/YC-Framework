<?php
/**
 * YC Framework
 * [zjdboy!] (C)2006-2009 zjdboy Inc. (http://YC.zjdboy.Net)
 *
 * 获取GET参数
 * 检测并增加addslashes
 *
 * @Package YC Framework
 * @Support http://www.yangguang.com
 * @Author  zjdboy <zjdboy@163.com>
 * @version $Id: yc.dopost.php 3 2011-12-29 15:01:09Z zjdboy $
 **/

//$_POST = array_change_key_case($_POST);
if (get_magic_quotes_gpc()) {

    function doPost($name) {
        if (isset($_POST[$name])) {
            return is_array($_POST[$name]) ? $_POST[$name] : trim($_POST[$name]);
        } else {
            return null;
        }
    }
} else {

    function doPost($name) {
        if (isset($_POST[$name])) {
            return is_array($_POST[$name]) ? $_POST[$name] : addslashes(trim($_POST[$name]));
        } else {
            return null;
        }
    }
}

?>
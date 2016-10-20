<?php

/**
 * YC Framework
 * [zjdboy!] (C)2006-2009 Eduu Inc. (http://www.eduu.com)
 *
 * 检测串中是否包含中文
 *
 * @param string $str 需要检测的字符串
 * @return bool 包含中文为true 否则为false
 * @--------------------------------
 * @Package zjdboyYC
 * @Support http://www.yangguang.com
 * @Author  zjdboy <zjdboyhr@gmail.com>
 * @version $Id: yc.isgbk.php 3 2011-12-29 15:01:09Z zjdboy $
 **/
function isgbk($str) {
    for ($i = 0, $j = strlen($str); $i < $j; $i ++) {
        if (ord($str{$i}) > 0xa0) return true;
    }
    return false;
}
?>
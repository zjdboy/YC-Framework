<?php

/**
 * YC Framework
 * [zjdboy!] (C)2006-2009 zjdboy Inc. (http://YC.zjdboy.Net)
 *
 * 获取一个自定义字符集合的随机数
 *
 * @param int    $len  随机取得长度
 * @param string $chr  只能为单字节字符
 * @return string
 * @--------------------------------
 * @Package YC Framework
 * @Support http://www.yangguang.com
 * @Author  zjdboy <zjdboy@163.com>
 * @version $Id: yc.dorand.php 3 2011-12-29 15:01:09Z zjdboy $
 **/
function doRand($len, $chr = '0123456789abcdefghigklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWSYZ') {
    $hash = null;
    $max = strlen($chr) - 1;
    for ($i = 0; $i < $len; $i ++) {
        $hash .= $chr{mt_rand(0, $max)};
    }
    return $hash;
}
?>
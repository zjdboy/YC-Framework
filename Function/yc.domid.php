<?php

/**
 * YC Framework
 * [zjdboy!] (C)2006-2009 zjdboy Inc. (http://YC.zjdboy.Net)
 *
 * 检测是否在设定的两个数之间
 * 结果总是出现在边界
 * 例如:
 * domid(985,0,100)=100 无边界设置
 * domid(985,0,100,20,96)=96 大边界
 * domid(0,0,100,20,96)=20 小边界
 *
 * @param int $it     一个整数
 * @param int $min    边界,较小的数
 * @param int $max    边界,较大的数
 * @param int $min_de 小边界的默认数值
 * @param int $max_de 大边界的默认数值
 * @return int
 * @--------------------------------
 * @Package YC Framework
 * @Support http://www.yangguang.com
 * @Author  zjdboy <zjdboy@163.com>
 * @version $Id: yc.domid.php 3 2011-12-29 15:01:09Z zjdboy $
 **/
function doMid($it, $min, $max, $min_de = null) {
    $it = (int) $it;
    if (null !== $min_de && $it == 0) {
        $it = $min_de;
    } else {
        $it = max($it, $min);
        $it = min($it, $max);
    }
    return $it;
}
?>
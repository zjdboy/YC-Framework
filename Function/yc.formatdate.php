<?php

/**
 * 格式化时间
 **/
function formatdate($t, $f = 'Y-m-d') {
    $m = $_SERVER['REQUEST_TIME'] - $t;
    if ($m <= 60) {
        return '刚刚';
    } elseif ($m > 60 and $m <= 3600) {
        $m = ceil($m / 60);
        return $m . '分钟前';
    } elseif ($m > 3600 and $m <= 86400) {
        $m = ceil($m / 3600);
        return $m . '小时前';
    } elseif ($m > 86400 and $m <= 2592000) {
        $m = ceil($m / 86400);
        return $m . '天前';
    } else {
        return date($f, $t);
    }
}
?>
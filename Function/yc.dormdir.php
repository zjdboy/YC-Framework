<?php

/**
 * YC Framework
 * [zjdboy!] (C)2006-2009 zjdboy Inc. (http://YC.zjdboy.Net)
 *
 * 删除指定目录
 * 注意: 这将清理目录下所有存在的子目录或文件,慎重执行
 *
 * @param string $sdir 一个目录的物理路径
 * @return boolen false执行失败,true表示清理成功
 * @--------------------------------
 * @Package zjdboyYC
 * @Support http://bbs.zjdboy.net
 * @Author  zjdboy <zjdboyhr@gmail.com>
 * @version $Id: yc.dormdir.php 3 2011-12-29 15:01:09Z zjdboy $
 **/
function doRmDir($sdir) {
    if (! is_dir($sdir)) return true;
    $cwd = getcwd();
    if (! chdir($sdir)) return false;
    
    $fs = dir('./');
    while (false !== ($entry = $fs->read())) {
        if ($entry == '.' || $entry == '..') continue;
        if (is_dir($entry)) {
            if (! doRmDir($entry)) return false;
        } else {
            if (! unlink($entry)) return false;
        }
    }
    $fs->close();
    
    if (! chdir($cwd)) return false;
    if (! rmdir($sdir)) return false;
    return true;
}
?>
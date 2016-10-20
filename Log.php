<?php

/**
 * YC Framework
 * [zjdboy!] (C)2006-2016 zjdboy Inc. 
 *
 * 写入日志
 *
 * @Package YC Framework
 * @Author  zjdboy <zjdboy@163.com>
 * @version $Id$
 **/
class YC_Log {

    /**
     * 写入日志
     * 
     * @param string $msg 日志内容
     * @param string $file 文件名
     * @param int $debug 是否直接显示，0不显示，1直接抛出
     * @return void
     *
     */
    public static function write($msg, $file = 'yc.log', $debug = 0) {
        // 将数据转换为字符串
        is_array($msg) && $msg = var_export($msg, true);
        
        // 如果是debug则直接输出
        if ($debug) {
            echo $msg . "\n";
        }
        
        // 写入日志
        file_put_contents($GLOBALS['_cfg']['sys_cache'] . $file, date("Y-m-d H:i:s > ") . "{$msg}\n", FILE_APPEND);
    }
}
?>
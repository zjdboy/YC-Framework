<?php

/**
 * YC Framework
 * [zjdboy!] (C)2006-2016 zjdboy Inc. 
 *
 * 自动载入函数模块 [Auto Load Function]
 * 函数处理在框架中开发,动态的加载外部函数
 *
 * @Package YC Framework
 * @Support http://www.yangguang.com
 * @Author  zjdboy <zjdboy@163.com>
 * @version $Id: Init.php 3 2011-12-29 15:01:09Z zjdboy $
 **/
class YC_Db_Init {

    public static $in = NULL;

    public static function factory($dbClass) {
        if (! isset(self::$in)) {
            $dbClass = 'YC_Db_' . $dbClass;
            self::$in = new $dbClass();
        }
        return self::$in;
    }
}

?>
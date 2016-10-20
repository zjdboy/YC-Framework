<?php
/**
 * YC Framework
 * [zjdboy!] (C)2006-2016 zjdboy Inc. 
 *
 * 数据库加载器,通过该模块进行加载与切换数据库应用
 * 如MYSQL MYSQLI Sqlserver等
 * 注意: 所有对象必须符合YC_Db_Base标准
 *
 * @Package YC Framework
 * @Support http://www.yangguang.com
 * @Author  zjdboy <zjdboy@163.com>
 * @version $Id: Db.php 13 2012-02-12 15:36:56Z zjdboy $
 **/
! defined('YC_DBC') && define('YC_DBC', 'Mysqli');

class YC_Db {

    /**
     * 工厂模式 静态激活对象
     * 
     * @param string $dbc 数据库类型
     * @param string $r 连接标识
     * @return object
     *
     */
    public static function factory($r = null, $dbc = null) {
        static $_obj = array();
        null === $r && $r = '-1';
        
        //是否需要重新连接
        if (! isset($_obj[$r])) {
            
            null === $dbc && $dbc = YC_DBC;
            $dbc = 'YC_Db_' . ucfirst($dbc);
            
            $_obj[$r] = & new $dbc();
            $r >= 0 && $_obj[$r]->getConn($r);
        }
        
        return $_obj[$r];
    }
}
?>
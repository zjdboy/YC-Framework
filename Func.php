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
 * @version $Id: Func.php 3 2011-12-29 15:01:09Z zjdboy $
 **/
class YC_Func {

    private $fdir = null;

    private $fitem = array();

    private static $in = null;

    /**
     * 对象静态初始化并载入存储在[Function]目录的函数库
     * @Example:
     * 静态加载: YC_Func::factory('dopost')
     * 静态加载: YC_Func::factory('dopost','doget')
     * 动态加载: YC_Func::factory()->dopost();
     * 
     * @param string 函数名,多个
     * @return Object
     *
     */
    public static function factory() {
        if (null === self::$in) {
            self::$in = new self();
            self::$in->fdir = YC_ROOT . 'Function/';
            $GLOBALS['_YC_FUNC'] = &self::$in->fitem;
        }
        $args = func_get_args();
        foreach ($args as $v)
            self::$in->isFunction($v);
        return self::$in;
    }

    /**
     * 等同于 factory 方法
     * 注意: 该方法准备废弃,在下一版本中废弃
     */
    public static function Init() {
        if (null === self::$in) {
            self::$in = new self();
            self::$in->fdir = YC_ROOT . 'Function/';
            $GLOBALS['_YC_FUNC'] = &self::$in->fitem;
        }
        $args = func_get_args();
        foreach ($args as $v)
            self::$in->isFunction($v);
        return self::$in;
    }

    /**
     * 检测并载入函数,私密方法供内部使用
     * 
     * @param string 函数名
     * @return null
     *
     */
    private function isFunction($fn) {
        $fn = strtolower($fn);
        if (! in_array($fn, $this->fitem)) {
            if (is_file($this->fdir . 'yc.' . $fn . '.php')) {
                include ($this->fdir . 'yc.' . $fn . '.php');
            } else {
                trigger_error("Not Found Function $fn()", E_USER_WARNING);
            }
            $this->fitem[] = $fn;
        }
    }

    /**
     * 魔法函数: 自动载入对象中不存在的方法
     * 
     * @param string 函数名
     * @return resource
     *
     */
    public function __call($fn, $fv) {
        self::isFunction($fn);
        return call_user_func_array($fn, $fv);
    }
}
?>
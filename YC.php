<?php
/**
 * YC Framework
 * [zjdboy!] (C)2006-2016 zjdboy Inc. 
 *
 * 路由器 YC-Framework框架的核心加载器
 * 框架中很多模块需要通过该对象进行激活
 *
 * @Package YC Framework
 * @Support http://www.yangguang.com
 * @Author  zjdboy <zjdboy@163.com>
 * @version $Id: YC.php 13 2012-02-12 15:36:56Z zjdboy $
 **/
! defined('YC_DS') && define('YC_DS', DIRECTORY_SEPARATOR);
! defined('YC_ROOT') && define('YC_ROOT', dirname(__FILE__) . YC_DS);
! defined('YC_LIBDIR') && define('YC_LIBDIR', dirname(YC_ROOT) . YC_DS);
! defined('YC_AUTOLOAD') && define('YC_AUTOLOAD', 'yc_autoload');

abstract class YC {

    public $version = '0.0.1';

    /**
     * 默认自动被执行的方法,可被重载
     */
    public function Init() {
    }

    /**
     * 重载配置合并到系统配置中
     * 
     * @param string $fn 缓存文件名称包括后缀
     * @return array 配置集合
     */
    protected function getCfg($fn = null) {
        $fn && $this->cfg = array_merge($this->cfg, (array) YC_Cache::get($fn));
        return $this->cfg;
    }

    /**
     * 注册变量到模板
     * 注意: 在函数体内的变量实现完全拷贝,会重复占用内存以及CPU资源
     * 建议使用refVar引用传递
     * 
     * @param string $strVar 变量指针
     * @param string $tplVar 模板中的变量名称
     * @param integer $tp 是否引用注册
     */
    protected function regVar($strVar, $tplVar = 'tmy') {
        $this->tpl->assign($tplVar, $strVar);
    }

    /**
     * 引用变量到模板
     * 注意: 呼叫时参量1必须为变量,不能为常量
     * 是一种节省资源的传递方式
     * 
     * @param string $strVar 变量指针
     * @param string $tplVar 模板中的变量名称
     * @param integer $tp 是否引用注册
     */
    protected function refVar(&$strVar, $tplVar = 'tmy') {
        $this->tpl->assignbyref($tplVar, $strVar);
    }

    /**
     * 处理模板并回显
     * 
     * @param string $tplVar 模板文件名称
     */
    protected function showView($tplVar) {
        $this->tpl->display($tplVar);
    }

    /**
     * 魔法方法: 动态载入全局变量 当变量不存在时试图创建之
     * 
     * @param string $k 变量名称
     * @return variable 变量内容
     */
    public function &__get($k) {
        ! isset($GLOBALS['_' . $k]) && self::__set($k);
        return $GLOBALS['_' . $k];
    }

    /**
     * 魔法方法: 动态创建全局变量 被成功创建的变量保存在GLOBALS中
     * Example : $this->var1=123 对象中var1不存在时自动创建到$GLOBALS['_var1']中
     * 
     * @param string $k 变量名称
     * @param string $v 变量值
     */
    public function __set($k, $v = null) {
        if (! isset($GLOBALS['_' . $k])) { //初始化系统变量
            if (isset($this->YC_REG_FUNC[$k])) {
                $v = $this->YC_REG_FUNC[$k]();
            } else {
                $GLOBALS['_' . $k] = &$v;
            }
        }
        $GLOBALS['_' . $k] = $v; //设置临时变量
    }

    /**
     * 魔法方法: 检测被动态创建的变量也可以是全局变量GLOBALS
     * Example : isset($this->var1) = isset($GLOBALS['_var1'])
     * 
     * @param string $k 变量名称
     */
    public function __isset($k) {
        return isset($GLOBALS['_' . $k]);
    }

    /**
     * 魔法方法: 释放变量资源
     * Example : unset($this->var1) = unset($GLOBALS['_var1'])
     * 
     * @param string $k 变量名称
     */
    public function __unset($k) {
        unset($GLOBALS['_' . $k]);
    }

    /**
     * 设置异常消息
     * 
     * @param string $msg 异常消息
     * @param string $code 错误代码
     */
    protected function showTry($msg, $code = __LINE__) {
        throw new YC_Exception($msg, $code, true);
    }
}

/**
 * 魔法函数: 自动加载对象文件
 * 注意: 带YC前缀的对象 通过YC_ROOT设置的目录加载
 * 其他前缀对象 通过YC_LIBDIR设置的路径加载
 * 
 * @param string $fname 对象名称
 *        $fn=YC_LIBDIR.str_replace('_','/',$fn);
 */
function YC_AutoLoad($fname) {
    $fn = explode('_', $fname);
    $fn[0] = $fn[0] == 'YC' ? YC_ROOT : YC_LIBDIR . $fn[0];
    $fn = join('/', $fn);
    
    if (! is_file($fn . '.php')) { //捕捉异常
        eval("class $fname{};"); //临时定义一个目标对象
        throw new YC_Exception("Has Not Found Class $fname");
    } else {
        require_once ($fn . '.php');
    }
}
spl_autoload_register(YC_AUTOLOAD);
?>
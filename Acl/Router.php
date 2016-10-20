<?php

/**
 * YC Framework
 * [zjdboy!] (C)2006-2016 zjdboy Inc. 
 *
 * 路由器
 * 重新定位模块 进行软路由
 * 同时可以定制路由表权限
 *
 * @Package YC Framework
 * @Support http://www.yangguang.com
 * @Author  zjdboy <zjdboy@163.com>
 * @version $Id: Router.php 2016-07-01 15:00:09Z zjdboy $
 **/
class YC_Acl_Router extends YC_Acl {

    public function __construct() {
    }

    /**
     * 开始路由
     * 
     * @param integer $tp 报告级别,1-3
     * @param integer $sp 是否返回串
     * @return string|echo
     *
     */
    public function toRoute(array &$route) {
        //指针向后移动一位
        $this->uri[1] = $this->uri[0];
        array_shift($this->uri);
        $this->uri[1] = $route['module'];
        $controller = &$this->uri[2];
        
        if ($controller && isset($route['controller']) && is_array($route['controller']) && ! in_array($controller, $route['controller'])) {
            //请求的方法未被允许无权访问
            throw new YC_Acl_Exception("Not Found Modules: {$controller}", 403);
        }
    }
}
?>
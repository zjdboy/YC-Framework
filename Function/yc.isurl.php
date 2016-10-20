<?php

/**
 * YC Framework
 * [zjdboy!] (C)2006-2009 Eduu Inc. (http://www.eduu.com)
 *
 * 检测是否为正常的URL
 *
 * @param string $url 需要处理的URL
 * @return bool
 * @--------------------------------
 * @Package YC Framework
 * @Support http://www.yangguang.com
 * @Author  zjdboy <zjdboy@163.com>
 * @version $Id: yc.isurl.php 3 2011-12-29 15:01:09Z zjdboy $
 **/
function isUrl($url) {
    return preg_match('/^(http|https):\/\/([\w-]+\.)+[\w-]+([\/|?]?[^\s]*)*$/i', $url) ? true : false;
}
?>
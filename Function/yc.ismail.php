<?php

/**
 * YC Framework
 * [zjdboy!] (C)2006-2009 Eduu Inc. (http://www.eduu.com)
 *
 * 检测是否为Email
 *
 * @param string $str 电子邮件Email
 * @return bool 成功则是true
 * @--------------------------------
 * @Package zjdboyYC
 * @Support http://www.yangguang.com
 * @Author  zjdboy <zjdboyhr@gmail.com>
 * @version $Id: yc.ismail.php 3 2011-12-29 15:01:09Z zjdboy $
 **/
function ismail($str) {
    return preg_match('/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/', $str);
}

?>
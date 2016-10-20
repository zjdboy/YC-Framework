<?php

/**
 * YC Framework
 * [zjdboy!] (C)2006-2009 Eduu Inc. (http://www.eduu.com)
 *
 * 简单检测是否是手机号
 *
 * @Package zjdboyYC
 * @Support http://www.yangguang.com
 * @Author  Yuelong <yuelonghu@100tal.com>
 * @version $Id$
 **/
function isPhone($phone) {
    if (is_numeric($phone) && substr($phone, 0, 1) == 1 && strlen($phone) == 11) {
        return TRUE;
    }
    return FALSE;
}
?>
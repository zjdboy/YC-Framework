<?php
/**
 * YC Framework
 * [zjdboy!] (C)2006-2016 YC Inc. (http://www.yangguang.com)
 *
 * 标签管理
 * 更加给定的关键词创建标签库
 *
 * 对码---
 * ASCII   [1]0x00-0x7F(0-127)
 * GBK     [1]0x81-0xFE(129-254) [2]0x40-0xFE(64-254)
 * GB2312  [1]0xB0-0xF7(176-247) [2]0xA0-0xFE(160-254)
 * Big5    [1]0x81-0xFE(129-255) [2]0x40-0x7E(64-126)| 0xA1－0xFE(161-254)
 * UTF8    单字节 0x00-0x7F(0-127) 多字节 [1]0xE0-0xEF(224-239) [2]0x80-0xBF(128-191) [3]0x80-0xBF(128-191)
 *
 * @Package YC Framework
 * @Support http://www.yangguang.com
 * @Author  zjdboy <zjdboy@163.com>
 * @version $Id: Tags.php 13 2012-02-12 15:36:56Z zjdboy $
 **/
define('YC_SPACETAGS', ' ');//标签的间隔符
class YC_Fcws_Tags extends YC_Fcws_Db {

    private $charset = 'gbk';

    private $loops = 1;

    /**
     * 预留方法 扩展使用
     * 
     * @return object
     *
     */
    private static $in = null;

    public static function Factory() {
        if (null === self::$in) self::$in = new self();
        return self::$in;
    }

    /**
     * 写入词典库
     * 
     * @param string $key 关键词
     * @param string $value 键对应的值
     * @return string
     */
    public function put($key, $value) {
        $value = strtolower($value); //不区分大小写
        $value = explode(YC_SPACETAGS, $value);
        
        foreach ($value as &$_key) {
            if (empty($_key)) continue;
            
            //关键字对应的标签
            $rsc = parent::get($_key); //检测是否已经有记录
            if ($rsc === false || $rsc == '9') {
                $_tmp = $key;
            } elseif (false === stripos($rsc, $key)) {
                $_tmp = $rsc . YC_SPACETAGS . $key;
            } else {
                //这里处理一个关键词可能对应多个标签
                $_tmp = $rsc . YC_SPACETAGS . $key;
                $_tmp = explode(YC_SPACETAGS, $_tmp);
                $_tmp = array_unique($_tmp);
                $_tmp = join(YC_SPACETAGS, $_tmp);
            }
            parent::put($_key, $_tmp);
            
            //保存半字
            $_len = strlen($_key);
            for ($i = 0; $i < $_len; ++ $i) {
                $old = ord($_key[$i]);
                $_tmp = null;
                if ($old < 0x80) {
                    if (($i + 1) < $_len && ord($_key[$i + 1]) < 0x80) continue;
                    $_tmp = substr($_key, 0, $i + 1);
                } else {
                    $i += $this->loops;
                    $_tmp = substr($_key, 0, $i + 1);
                }
                ! parent::get($_tmp) && parent::put($_tmp, 9);
            }
        }
    }

    /**
     * 检测给定的串中是否有关键词包含在字库中
     * 如果找到则返回被找到的第一个关键词,否则返回false
     * 
     * @param string $str 需要处理的字符串
     * @param int $mx 取得的标签数量
     * @return string
     */
    public function get($str, $mx = 10) {
        $str = strtolower($str); //不区分大小写
        $len = strlen($str);
        $item = array();
        
        //双重跑马灯模式进行检测
        for ($i = 0; $i < $len; $i += $_spa) {
            $_c = $str[$i];
            $_o = ord($_c);
            if ($_o < 0x80) { //单字节
                $_spa = 1; //起点跑马
                if (self::_is_en_token($_o)) continue; //标点符号英文
            } else { //双字节
                $_spa = $this->loops + 1; //起点跑马
                $_c .= substr($str, $i + 1, $this->loops);
                if (false === parent::get($_c)) continue; //不会存在词
            }
            
            //第二重跑马
            $_len = ($len - $i) < $this->_keymax ? ($len - $i) : $this->_keymax;
            for ($j = $_spa; $j < $_len; ++ $j) {
                $_o = ord($str[$i + $j]);
                if ($_o < 0x80) { //单字节
                    

                    //当相邻的字符均是单字节字符,且非符号时,进行连串处理
                    if (! self::_is_en_token($_o) && $j < $_len - 1 && ord($str[$i + $j + 1]) < 0x80 && ! self::_is_en_token(ord($str[$i + $j + 1]))) continue;
                    $_spa == 1 && $_spa = $j + 1;
                } else { //双字节
                    $j += $this->loops;
                }
                $_tmp = substr($str, $i, $j + 1);
                $_res = parent::get($_tmp);
                if (false === $_res) {
                    break; //不存在词
                } elseif ($_res == 9) {
                    continue;
                }
                $_res = explode(YC_SPACETAGS, $_res);
                foreach ($_res as &$v)
                    $item[$v] = $v;
                    //是否满足了条目
                if (count($item) >= $mx) {
                    $item = array_slice($item, 0, $mx);
                    break 2;
                }
            }
        }
        return join(YC_SPACETAGS, $item);
    }

    /**
     * 设置字符集
     * 
     * @param string $str 字符集
     * @return void
     *
     */
    public function SetChar($str) {
        $str = strtolower($str);
        $str == 'utf-8' && $str = 'utf8';
        $this->charset = $str;
        $str == 'utf8' && $this->loops = 2;
    }

    /**
     * 检测是否存在英文标点符号,空格与回车
     * 
     * @param string $str 字符
     * @return bool
     *
     */
    private function _is_en_token($_o) {
        return ($_o <= 47 || ($_o >= 58 && $_o <= 64) || ($_o >= 91 && $_o <= 96) || $_o >= 123);
    }
}
?>
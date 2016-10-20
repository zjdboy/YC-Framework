<?php
/**
 * YC Framework
 * [zjdboy!] (C)2006-2016 YC Inc. (http://www.yangguang.com)
 *
 * 违禁词管理
 * 导入导出词库，根据词库识别关键词的级别
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
 * @version $Id: Split.php 13 2012-02-12 15:36:56Z zjdboy $
 **/
define('YC_SPACESPLIT', '#');//默认值
class YC_Fcws_Split extends YC_Fcws_Db {

    private $charset = 'gbk';

    private $loops = 1;

    /**
     * 预留方法 扩展使用
     * 
     * @return object
     *
     */
    public static function Factory() {
        return new self();
    }

    /**
     * 写入词典库
     * 储存格式为以第一个完整字符开始递拆串例如:
     * 开发工具VC -> 开 开发 开发工 开发工具 开发工具VC
     * 
     * @param string $key 关键词
     * @param string $value 键对应的值
     * @return string
     */
    public function put($key, $value) {
        $key = strtolower($key); //不区分大小写
        $_len = strlen($key);
        for ($i = 0; $i < $_len; ++ $i) {
            $old = ord($key[$i]);
            $_tmp = null;
            if ($old < 0x80) {
                if (($i + 1) < $_len && ord($key[$i + 1]) < 0x80) continue;
                $_tmp = substr($key, 0, $i + 1);
            } else {
                $i += $this->loops;
                $_tmp = substr($key, 0, $i + 1);
            }
            ! parent::get($_tmp) && parent::put($_tmp, YC_SPACESPLIT);
        }
        parent::put($key, $value);
    }

    /**
     * 检测给定的串中是否有关键词包含在字库中
     * 如果找到则返回被找到的第一个关键词,否则返回false
     * 
     * @param str $str 需要处理的字符串
     * @param int $rank 关键词级别,发现有小于等于rank值的词立即停止处理
     * @param int $mx 采取可疑词列表个数
     * @return string
     */
    public function get($str, $rank = 0, $mx = 5) {
        $item = array(
            'res' => false, //大于0为找到词，值词的级别
            'msg' => array(), //敏感词列表，最后一个词总是禁用词
            'msgs' => 0
        ); //找到的敏感词个数
        

        $str = strtolower($str); //不区分大小写
        $len = strlen($str);
        
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
                    //不存在词
                    break;
                } elseif ($_res == YC_SPACESPLIT) {
                    //有可能，继续查询下面字节
                    continue;
                }
                
                // 找到词了
                $item['msgs'] ++;
                if ($_res <= $rank) { //找到设定级别的关键词
                    $item['res'] = true;
                    $item['msg'][$_tmp] = $_tmp;
                    if ($mx == 0 && count($item['msg']) > 1) {
                        array_shift($item['msg']);
                    }
                    break 2;
                } elseif ($mx > 0) { //低级别词
                    $item['msg'][$_tmp] = $_tmp;
                    $mx --;
                    continue;
                }
            }
        }
        return $item;
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
     * 检测是否存在英文标点符号
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
<?php
/**
 * YC Framework
 * [zjdboy!] (C)2006-2016 zjdboy Inc. 
 *
 * 缓存对象 主要用于生产配置文件,缓存数据
 * 如将页面部分信息,数据库查询结果集缓存到文件
 *
 * 注意:有该模块写入的缓存文件,不能人为编辑修改,否则将无法读取缓存文件
 *
 * @Package YC Framework
 * @Support http://www.yangguang.com
 * @Author  zjdboy <zjdboy@163.com>
 * @version $Id: Fcache.php 3 2011-12-29 15:01:09Z zjdboy $
 **/
! defined('YC_FCACHE_LIFE') && define('YC_FCACHE_LIFE', 31536000);
//默认一年
class YC_Cache_Fcache extends YC {

    public $mc;
    //连接成功时的标识
    //Fcache配置文件
    private $fc = array(
        'froot' => '/tmp/', //缓存跟目录
        'type' => 0, //采用什么方式储存
        'fmod' => 0755, //写入文件的权限
        'fext' => '.php', //写入文件的后缀
        'fdef' => array()
    );
    //默认结果集合
    private static $in = null;

    /**
     * 预留方法 扩展使用
     */
    public static function Factory() {
        if (null === self::$in) self::$in = new self();
        return self::$in;
    }

    /**
     * 初始化FC对象
     * 
     * @param string $var1 参数说明
     * @param string $var2 参数说明
     * @return array $tplPre 模板后缀
     */
    public function __construct() {
        isset($this->fccfg) && $this->fc = array_merge($this->fc, $this->fccfg); //重组配置文件
    }

    /**
     * 设置数据缓存
     * 与add|replace比较类似
     * 唯一的区别是: 无论key是否存在,是否过期都重新写入数据
     * 
     * @param string $key 数据的标识
     * @param string $value 实体内容
     * @param string $expire 过期时间[天d|周w|小时h|分钟i] 如:8d=8天 默认为0永不过期
     * @param bool $iszip 是否启用压缩
     * @return bool
     *
     */
    public function set($key, $value, $expire = 0, $iszip = false) {
        $key = $this->_fpath($key, 1);
        $value = "<?php\n" . date('//Y-m-d H:i:s') . "\n return " . var_export($value, true) . ";\n?>";
        $value = file_put_contents($key, $value);
        self::_setLife($key, $expire);
        return $value;
    }

    /**
     * 获取数据缓存
     * 
     * @param string $key 缓存文件名称包括后缀
     * @param integer $t 已被废弃--缓存时间设置(单位秒),取得多长时间内写入的缓存
     * @return string|array
     *
     */
    public function get($key) {
        $key = $this->_fpath($key);
        if (! is_file($key) || ! self::_isLife($key)) return $this->fc['fdef'];
        return include ($key);
    }

    /**
     * 新增数据缓存
     * 当key不存在或存在且已过期时被设值
     * 
     * @param string $key 数据的标识
     * @param string $value 实体内容
     * @param string $expire 过期时间[天d|周w|小时h|分钟i] 如:8d=8天 默认为0永不过期
     * @param bool $iszip 是否启用压缩
     * @return bool 操作成功时返回ture,如果存在返回false否则返回true
     */
    public function add($key, $value, $expire = 0, $iszip = false) {
        $key = $this->_fpath($key, 1);
        if (is_file($key) && self::_isLife($key)) return false; //缓存未失效直接返回
        $value = "<?php\n" . date('//Y-m-d H:i:s') . "\n return " . var_export($value, true) . ";\n?>";
        $value = file_put_contents($key, $value);
        self::_setLife($key, $expire);
        return $value;
    }

    /**
     * 替换数据缓存
     * 与 add|set 参数相同,与set比较类似
     * 唯一的区别是: 只有当key存在且未过期时才能被设值
     * 
     * @param string $key 数据的标识
     * @param string $value 实体内容
     * @param string $expire 过期时间[天d|周w|小时h|分钟i] 如:8d=8天 默认为0永不过期
     * @param bool $iszip 是否启用压缩
     * @return bool
     *
     */
    public function replace($key, $value, $expire = 0, $iszip = false) {
        $key = $this->_fpath($key);
        if (! is_file($key) || ! self::_isLife($key)) return false; //文件不存在
        $value = "<?php\n" . date('//Y-m-d H:i:s') . "\n return " . var_export($value, true) . ";\n?>";
        $value = file_put_contents($key, $value);
        self::_setLife($key, $expire);
        return $value;
    }

    /**
     * 检测缓存是否存在
     * 
     * @param string $key 数据的标识
     * @return bool
     *
     */
    public function isKey($key) {
        $key = $this->_fpath($key);
        if (is_file($key)) {
            return self::_isLife($key);
        } else {
            return false;
        }
    }

    /**
     * 删除数据缓存
     * 
     * @param string $key 数据的标识
     * @param string $expire 删除的等待时间,好像有问题尽量不要使用
     * @return bool
     *
     */
    public function del($key, $expire = 0) {
        return @unlink($this->_fpath($key));
    }

    /**
     * 获取存储路径
     * 
     * @param string $fn 缓存文件名称包括后缀
     * @param integer $t 是否建立目录,1不存在时创建之|0不做检测 默认为0
     * @return string 系统完整的路径
     */
    private function _fpath($fn, $t = 0) {
        $fpath = str_replace('_', '/', $fn, $i);
        if ($i <= 0) { //没有子目录
            $fpath = $this->fc['froot'] . $fpath;
        } else {
            $fpath = dirname($fpath);
            $t && ! is_dir($this->fc['froot'] . $fpath) && mkdir($this->fc['froot'] . $fpath, $this->fc['fmod'], true);
            $fpath = $this->fc['froot'] . $fpath . '/' . $fn;
        }
        return $fpath . $this->fc['fext'];
    }

    /**
     * 检测否过期
     * 
     * @param string $key 文件标识
     * @return bool 1表示未过期|0标识已过期
     */
    private function _isLife($key) {
        $fm = filemtime($key);
        if ($fm > 0 && $fm <= time()) return false;
        return true;
    }

    /**
     * 设置文件过期时间
     * 
     * @param string $key 文件标识
     * @param integer $expire 过期时间
     * @return void
     *
     */
    private function _setLife($key, $expire) {
        if (! is_numeric($expire)) {
            switch (substr($expire, - 1)) {
                case 'w': //周
                    $expire = (int) $expire * 7 * 24 * 3600;
                    break;
                case 'd': //天
                    $expire = (int) $expire * 24 * 3600;
                    break;
                case 'h': //小时
                    $expire = (int) $expire * 3600;
                    break;
                case 'i': //分钟
                    $expire = (int) $expire * 60;
                    break;
                default:
                    $expire = (int) $expire;
                    break;
            }
        }
        
        touch($key, $expire > 0 ? $expire + time() : time() + YC_FCACHE_LIFE);
    }

    /**
     * 设置异常消息 可以通过try块中捕捉该消息
     * 
     * @param resource $query 资源标识指针
     * @return boolean
     *
     */
    private function showMsg($str) {
        throw new YC_Exception($str);
    }
}

?>
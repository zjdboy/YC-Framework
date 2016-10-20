<?php

/**
 * YC Framework
 * [zjdboy!] (C)2006-2016 zjdboy Inc. 
 *
 * 缓存对象 主要用于生产配置文件,缓存数据
 * 如将页面部分信息,数据库查询结果集缓存到文件
 * delect bug=0
 *
 * @Package YC Framework
 * @Support http://www.yangguang.com
 * @Author  zjdboy <zjdboy@163.com>
 * @version $Id: Redis.php 13 2012-02-12 15:36:56Z zjdboy $
 **/
class YC_Cache_Redis extends YC {

    public $mc;
    //连接成功时的标识
    

    /**
     * 预留方法 扩展使用
     */
    public static function Factory() {
        return new self();
    }

    /**
     * 初始化对象
     * 
     * @return void
     *
     */
    public function __construct() {
        $this->mc = new Redis();
        $this->mc->connect(isset($this->rdscfg['host']) ? $this->rdscfg['host'] : '127.0.0.1', isset($this->rdscfg['port']) ? $this->rdscfg['port'] : '11211') or self::showMsg('[RedisCache:]Could not connect');
        
        // 校验是否需要密码
        if (isset($this->rdscfg['user']) && isset($this->rdscfg['pwd'])) {
            if ($this->mc->auth($this->rdscfg['user'] . ":" . $this->rdscfg['pwd']) == false) {
                self::showMsg('[RedisCache:]' . $this->mc->getLastError());
            }
        }
        
        ! isset($this->rdscfg['pre']) && $this->rdscfg['pre'] = '';
        $this->mc->select($this->rdscfg['dbs']);
    }

    /**
     * 与set方法相同
     * 唯一的区别是: 增加对数组序列化功能
     * 
     * @param string $key 数据的标识
     * @param string $value 实体内容
     * @param string $expire 过期时间单位秒
     * @return bool
     *
     */
    public function sets($key, $value, $expire = 0) {
        $expire > 0 && $expire = self::setLifeTime($expire);
        return $expire > 0 ? $this->mc->setex($this->rdscfg['pre'] . $key, $expire, $value) : $this->mc->set($this->rdscfg['pre'] . $key, $value);
    }

    /**
     * 获取数据缓存
     * 
     * @param string $key 数据的标识
     * @return string
     *
     */
    public function gets($key) {
        return $this->mc->get($this->rdscfg['pre'] . $key);
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
    public function set($key, $value, $expire = 0) {
        $value = self::rdsCode($value, 1);
        $expire > 0 && $expire = self::setLifeTime($expire);
        return $expire > 0 ? $this->mc->setex($this->rdscfg['pre'] . $key, $expire, $value) : $this->mc->set($this->rdscfg['pre'] . $key, $value);
    }

    /**
     * 获取数据缓存
     * 
     * @param string $key 数据的标识
     * @return string
     *
     */
    public function get($key) {
        $value = $this->mc->get($this->rdscfg['pre'] . $key);
        return $value ? self::rdsCode($value) : $value;
    }

    /**
     * 获取数据集合
     * 
     * @param array $key 数据的标识
     * @param int $t 是否清除空记录
     * @return array
     *
     */
    public function mget(array $key, $t = 0) {
        $item = array();
        $_tmp = $this->mc->getMultiple($key);
        $_k = 0; //解决键名中带有ID的情况
        foreach ($key as $k => &$v) {
            $v = $_tmp[$_k];
            $v = $v ? self::rdsCode($v) : $v;
            ++ $_k;
            
            if ($t && empty($v)) continue;
            $item[$k] = &$v;
        }
        return $item;
    }

    /**
     * 新增数据缓存
     * 只有当key不存,存在但已过期时被设值
     * 
     * @param string $key 数据的标识
     * @param string $value 实体内容
     * @param string $expire 过期时间[天d|周w|小时h|分钟i] 如:8d=8天 默认为0永不过期
     * @param bool $iszip 是否启用压缩
     * @return bool 操作成功时返回ture,如果存在返回false否则返回true
     */
    public function add($key, $value, $expire = 0) {
        if ($expire > 0) {
            $expire = self::setLifeTime($expire);
            if ($this->mc->exists($this->rdscfg['pre'] . $key)) {
                return false;
            } else {
                return $this->set($key, $value, $expire);
            }
        } else {
            $value = self::rdsCode($value, 1);
            return $this->mc->setnx($this->rdscfg['pre'] . $key, $value);
        }
    }

    /**
     * 替换数据
     * 与 add|set 参数相同,与set比较类似
     * 唯一的区别是: 只有当key存在且未过期时才能被替换数据
     * 
     * @param string $key 数据的标识
     * @param string $value 实体内容
     * @param string $expire 过期时间[天d|周w|小时h|分钟i] 如:8d=8天 默认为0永不过期
     * @param bool $iszip 是否启用压缩
     * @return bool
     *
     */
    public function replace($key, $value, $expire = 0) {
        if (self::iskey($key)) {
            return self::set($key, $value, $expire);
        }
        return false;
    }

    /**
     * 检测缓存是否存在
     * 
     * @param string $key 数据的标识
     * @return bool
     *
     */
    public function isKey($key) {
        return $this->mc->exists($this->rdscfg['pre'] . $key);
    }

    public function getKey($key) {
        return $this->mc->keys($key);
    }

    /**
     * 删除一个数据缓存
     * 
     * @param string $key 数据的标识
     * @param string $expire 删除的等待时间,好像有问题尽量不要使用
     * @return bool
     *
     */
    public function del($key) {
        return $this->mc->del($this->rdscfg['pre'] . $key);
    }

    /**
     * 格式化过期时间
     * 注意: 限制时间小于2592000=30天内
     * 
     * @param string $t 要处理的串
     * @return int
     *
     */
    private function setLifeTime($t) {
        if (! is_numeric($t)) {
            switch (substr($t, - 1)) {
                case 'w': //周
                    $t = (int) $t * 7 * 24 * 3600;
                    break;
                case 'd': //天
                    $t = (int) $t * 24 * 3600;
                    break;
                case 'h': //小时
                    $t = (int) $t * 3600;
                    break;
                case 'i': //分钟
                    $t = (int) $t * 60;
                    break;
                default:
                    $t = (int) $t;
                    break;
            }
        }
        $t > 2592000 && $t = 2592000;
        //if($t>2592000) self::showMsg('Memcached Backend has a Limit of 30 days (2592000 seconds) for the LifeTime');
        return $t;
    }

    /**
     * 编码解码
     * 
     * @param string $str 串
     * @param string $tp 类型,1编码0为解码
     * @return array|string
     *
     */
    private function rdsCode($str, $tp = 0) {
        return $tp ? @serialize($str) : @unserialize($str);
    }

    /**
     * 设置异常消息 可以通过try块中捕捉该消息
     * 
     * @param resource $query 资源标识指针
     * @return boolean
     *
     */
    private function showMsg($str) {
        // YC_Log::write($str, "redis.log");
        throw new YC_Exception($str);
    }
}

?>
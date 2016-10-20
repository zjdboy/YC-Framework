<?php

/**
 * YC Framework
 * [zjdboy!] (C)2006-2016 zjdboy Inc. 
 *
 * Socket远程通讯模块
 *
 * Example: YC_Http_Fsock::Factory('time:5','size:30','loop:1','charset:gb2312')->get('http://http://www.yangguang.com');
 * @Package YC Framework
 * @Author  zjdboy <zjdboy@163.com>
 * @version $Id$
 **/
class YC_Http_Socket {

    protected $_timeout = 30;
    //连接超时时间
    protected $_loop = 0;
    //设置301重定向深度，0为关闭301
    protected $_ip = null;
    //设置主机IP地址，不设置则会走DNS解析
    protected $_size = 10240;
    //下载的数据大小(单位KB)
    protected $_method = null;
    //当前请求类型
    protected $_referer = null;
    //来访URL
    protected $_post = null;
    //post信息
    protected $_debug = 0;
    //调试信息
    protected $_agent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_2) AppleWebKit/537.36 Chrome/37.0.2062.94 YC/1.0';
    //访问头信息
    public static function factory() {
        return new self();
    }

    /**
     * 获取页面信息
     * 
     * @param string $url Url连接地址
     * @return array|string
     *
     */
    public function get($url) {
        // 返回数据
        $_data = null;
        $_head = null;
        
        //取得格式化URL得到 HOST REQUIST
        $url = str_replace(' ', '%20', $url);
        $_url = @parse_url($url);
        
        // debug
        $this->_debug && $this->wlog("\n\n" . date("Y-m-d H:i:s > ") . "{$url}\n");
        
        // 校验是否为有效URL请求
        if (empty($_url['scheme']) || empty($_url['host'])) return $_data; //非法地址
        

        // 识别请求类型
        switch ($_url['scheme']) {
            case 'https':
                $_url['scpact'] = 'ssl://';
                ! isset($_url['port']) && $_url['port'] = 443;
                break;
            case 'http':
            default:
                $_url['scpact'] = '';
                ! isset($_url['port']) && $_url['port'] = 80;
        }
        
        // 配置query信息
        ! isset($_url['path']) && $_url['path'] = '/';
        $_url['uri'] = empty($_url['query']) ? $_url['path'] : $_url['path'] . '?' . $_url['query'];
        $_url['url'] = $_url['scheme'] . '://' . $_url['host'];
        $_url['ip'] = empty($this->_ip) ? $_url['host'] : $this->_ip;
        
        // 设置POST数据
        $post = empty($this->_post) ? '' : http_build_query($this->_post);
        
        // 设置访问类型
        if (empty($this->_method)) {
            $method = empty($post) ? 'GET' : 'POST';
        } else {
            $method = $this->_method;
        }
        
        //定制request Header头信息
        $http_header = null;
        $http_header .= "{$method} {$_url['uri']} HTTP/1.0\r\n";
        $http_header .= "Host: {$_url['host']}\r\n";
        $http_header .= "User-Agent: {$this->_agent}\r\n";
        $http_header .= "User-FDIP: {$_url['ip']}:{$_url['port']}\r\n";
        ! empty($this->_referer) && $http_header .= "Referer: {$this->_referer} \r\n";
        if (! empty($post)) {
            $http_header .= 'Content-Length: ' . strlen($post) . "\r\n";
            $http_header .= "Content-Type: application/x-www-form-urlencoded\r\n";
        }
        $http_header .= "Cache-Control: no-cache\r\n";
        $http_header .= "Connection: close\r\n";
        $http_header .= "\r\n";
        $http_header .= $post;
        $this->_debug && $this->wlog("request:\n{$http_header}\n");
        
        //fsockopen(链接域名,端口,错误代码,错误详细信息,超时时间秒)
        $fp = @fsockopen($_url['scpact'] . $_url['ip'], $_url['port'], $errno, $errstr, $this->_timeout);
        
        //连接失败
        if (! $fp) {
            $this->wlog("{$url} [{$errstr}]\n");
            return $_data;
        }
        
        stream_set_blocking($fp, true); //设置为阻塞模式访问数据流
        socket_set_timeout($fp, $this->_timeout); //设置超时时间
        @fwrite($fp, $http_header); //已经连接上写入头信息//$CRLF="\x0d"."\x0a"."\x0d"."\x0a";
        

        $thd = true;
        //循环读取文件字节流
        $size = $this->_size * 1024; //得到字节数
        $loop = $this->_loop;
        $info = stream_get_meta_data($fp);
        
        while (! feof($fp) && (! $info['timed_out'])) {
            $tmp_stream = fgets($fp, 128); //读文件字节流
            $this->_debug && $this->wlog("{$tmp_stream}");
            $info = stream_get_meta_data($fp);
            
            //是否还在头
            if ($thd) {
                $_head .= $tmp_stream;
                
                //检测是否读取头信息结束
                if ($tmp_stream == "\r\n") {
                    if ($this->_method == 'head') break;
                    $thd = false;
                    continue;
                }
                
                // 检测是否有301跳转
                if ($loop <= 0 || false === stripos(strtolower($tmp_stream), 'location')) continue;
                
                //分析头信息
                if (! preg_match('/([^:]+):(.*)/i', $tmp_stream, $tmp_hd)) {
                    continue;
                }
                
                $tmp_hd[1] = strtolower(trim($tmp_hd[1]));
                $tmp_hd[2] = trim($tmp_hd[2]);
                
                //检测是否被转向
                if ($tmp_hd[1] == 'location') {
                    if (false !== stripos($tmp_hd[2], 'cncmax.cn')) break;
                    // 组装路径
                    if (substr($tmp_hd[2], 0, 7) != 'http://') {
                        if (substr($tmp_hd[2], 0, 1) == '/') {
                            $tmp_hd[2] = $_url['url'] . $tmp_hd[2]; //---/web/index.html
                        } else {
                            $tmp_hd[2] = $_url['url'] . substr($_url['path'], 0, strrpos($_url['path'], '/')) . '/' . $tmp_hd[2]; //--web/index.html
                        }
                    }
                    @fclose($fp); //关闭连接
                    $this->_referer = $url;
                    
                    // 跳转到其它玉米
                    if (false === stripos($tmp_hd[2], $_url['host'])) {
                        $this->_ip = null;
                    }
                    
                    // 继续loop
                    $this->setLoop(-- $loop);
                    $_data = $this->get($tmp_hd[2]); //开始跳转进行第二次尝试连接
                    break;
                }
            } else {
                $_data .= $tmp_stream;
                
                // 字节大小设置
                $size = $size - strlen($tmp_stream);
                if ($size <= 0) break;
            }
        }
        @fclose($fp);
        return $_data;
    }

    /**
     * 设置超时时间
     * 
     * @param int $t 超时时间，单位秒
     * @return void
     *
     */
    public function setTimeOut($t) {
        $this->_timeout = (int) $t;
    }

    /**
     * 设置允许301次数
     * 
     * @param int $t 次数，默认0
     * @return void
     *
     */
    public function setLoop($t) {
        $t = (int) $t;
        $t <= 0 && $t = 0;
        $this->_loop = $t;
    }

    /**
     * 设置允许读取最大的字节数
     * 
     * @param int $t 字节数，单位KB
     * @return void
     *
     */
    public function setSize($t) {
        $this->_size = (int) $t;
    }

    /**
     * 设置访问头信息
     * 
     * @param str $t useragent头信息，默认为系统自带
     * @return void
     *
     */
    public function setAgent($t) {
        $this->_agent = $t;
    }

    /**
     * 设置主机IP
     * 
     * @param str $t IP地址，默认为空则走DNS解析
     * @return void
     *
     */
    public function setIp($t) {
        $this->_ip = $t;
    }

    /**
     * 设置主机Referer
     * 
     * @param str $t 请求的来访地址，是一个URL
     * @return void
     *
     */
    public function setReferer($t) {
        $this->_referer = $t;
    }

    /**
     * 设置请求类型
     * 
     * @param str $t 请求类型：GET\POST\HEAD
     * @return void
     *
     */
    public function setMethod($t) {
        $this->_method = $t;
    }

    /**
     * 设置POST
     * 
     * @param array $t post内容
     * @return void
     *
     */
    public function setPost($t) {
        $this->_post = $t;
    }

    /**
     * 设置debug
     * 
     * @param int $t 是否开启debug，1开启，0关闭，默认关闭
     * @return void
     *
     */
    public function setDebug($t) {
        $this->_debug = $t;
    }
    
    // debug
    private function wlog($tmp) {
        if ($this->_debug == 2) {
            echo $tmp;
        } else {
            file_put_contents($GLOBALS['_cfg']['sys_cache'] . 'socket.log', $tmp, FILE_APPEND);
        }
    }
}
?>
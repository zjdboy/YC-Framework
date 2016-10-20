<?php

/**
 * YC Framework
 * [zjdboy!] (C)2006-2016 zjdboy Inc. 
 *
 * Soap远程通讯模块
 * 比如: 发送伪造的HEADER头至服务器,并实现模拟机器人访问站点
 *
 * Example: YC_Http_Fsock::Factory('time:5','size:30','loop:1','charset:gb2312')->get('http://http://www.yangguang.com');
 * @Package YC Framework
 * @Support http://www.yangguang.com
 * @Author  zjdboy <zjdboy@163.com>
 * @version $Id: Fsock.php 80 2012-04-03 16:11:25Z zjdboy $
 **/
class YC_Http_Soap {

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
    public function get($url, $urlaction, $method, $data, $ver = '1.0', $user = 'admin', $passwd = 'admin') {
        if ($ver == '1.0') {
            $xml_post_string = '
            <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:bee2="http://www.sinosig.com/Bee2cSrv/">
            <soapenv:Header/>
            <soapenv:Body>
            <bee2:Bee2cRequest>
            <method>' . $method . '</method>
            <data>' . $data . '</data>
            </bee2:Bee2cRequest>
            </soapenv:Body>
            </soapenv:Envelope>
            ';
        } else {
            $xml_post_string = '
            <soapenv:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:siit="http://siit-cn.com">
            <soapenv:Header/>
            <soapenv:Body>
            <siit:' . $method . ' soapenv:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
            <jsonparam xsi:type="soapenc:string" xs:type="type:string" xmlns:soapenc="http://schemas.xmlsoap.org/soap/encoding/" xmlns:xs="http://www.w3.org/2000/XMLSchema-instance">
            ' . $data . '
            </jsonparam>
            </siit:' . $method . '>
            </soapenv:Body>
            </soapenv:Envelope>
            ';
        }
        
        //Header信息
        $headers = array(
            "Content-Type: text/xml;charset=UTF-8",
            "Accept-Encoding: gzip,deflate",
            "Accept: text/xml",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
            "Connection: Keep-Alive",
            "User-Agent: Apache-HttpClient/6.6.6 (java 20210725)",
            "SOAPAction: " . $urlaction,
            "Content-length: " . strlen($xml_post_string)
        );
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $user . ":" . $passwd);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $res = curl_exec($ch);
        curl_close($ch);
        return $res;
    }
}

?>
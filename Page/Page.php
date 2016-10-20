<?php

/**
 * YC Framework
 * [zjdboy!] (C)2006-2016 zjdboy Inc. 
 *
 * 分页对象
 * 适用于动态URL分页 采用当前所在行作为分页标识
 *
 * @Package YC Framework
 * @Support http://www.yangguang.com
 * @Author  zjdboy <zjdboy@163.com>
 * @version $Id: Page.php 13 2012-02-12 15:36:56Z zjdboy $
 **/
class YC_Page_Page extends YC {

    public $psize = 0;
    //每页记录数
    public $total = 0;
    //总记录数
    public $pmax = 10;
    //输出的最大页数
    public $limit = '';
    //送出SQL分页标识
    public $pname = 'pg';
    //分页变量
    

    /**
     * 获取分页连接
     * 
     * @param integer $psize 每页显示数
     * @param string $pname 分页标识
     * @return string
     *
     */
    public function getPage($psum, $psize = 20) {
        $this->total = &$psum;
        $this->psize = &$psize;
        $this->psize <= 0 && $this->psize = 20;
        $this->pmax <= 5 && $this->pmax = 10;
        
        //得到当前分页游标-并计算当前页号
        $cpage = (int) @$_GET[$this->pname];
        $total = ceil($psum / $this->psize); //总页数
        $cpage > $total && $cpage = $total; //当前页数最大不能超过总页数
        $cpage <= 0 && $cpage = 1;
        
        //得到查询SQL
        $this->limit = 'LIMIT ' . (($cpage - 1) * $this->psize) . ',' . $this->psize;
        
        $stem = null;
        if ($total <= 1 || $psum <= $this->psize) return $stem; //分页总数小于分页基数
        

        //取得URL QUERY_STRING 所有参数并解码为数组集合
        $url_param = @$_SERVER['QUERY_STRING'];
        parse_str($url_param, $url_param);
        
        //分布显示方式
        $txpg = empty($this->cfg['sys_pgtx']) ? '&lt;&lt;' : $this->cfg['sys_pgtx'];
        $nxpg = empty($this->cfg['sys_pgnx']) ? '&gt;&gt;' : $this->cfg['sys_pgnx'];
        
        if ($total <= ($this->pmax + 2)) { //不存在缩进-直接显示所有分页
            $stem .= ' <a' . (($cpage - 1) > 0 ? ' href="?' . self::getParam($url_param, $cpage - 1) . '"' : '') . ">{$txpg}</a>";
            for ($i = 1; $i <= $total; $i ++) {
                $stem .= ($i == $cpage) ? " <b>{$i}</b>" : ' <a href="?' . self::getParam($url_param, $i) . "\">{$i}</a>";
            }
            $stem .= ' <a' . (($cpage + 1) <= $total ? ' href="?' . self::getParam($url_param, $cpage + 1) . '"' : '') . ">{$nxpg}</a>";
        } elseif ($cpage <= ($this->pmax - 2)) { //尾部缩进
            $stem .= ' <a' . (($cpage - 1) > 0 ? ' href="?' . self::getParam($url_param, $cpage - 1) . '"' : '') . ">{$txpg}</a>";
            for ($i = 1; $i <= $this->pmax; $i ++) {
                $stem .= ($i == $cpage) ? ' <b>' . $i . '</b>' : ' <a href="?' . self::getParam($url_param, $i) . '" >' . $i . '</a>';
            }
            $stem .= ' <span>...</span>';
            $stem .= ' <a href="?' . self::getParam($url_param, $total - 1) . '" >' . ($total - 1) . '</a>';
            $stem .= ' <a href="?' . self::getParam($url_param, $total) . '" >' . $total . '</a>';
            $stem .= ' <a' . (($cpage + 1) <= $total ? ' href="?' . self::getParam($url_param, $cpage + 1) . '"' : '') . ">{$nxpg}</a>";
        } elseif ($cpage > 2 && $cpage < ($total - $this->pmax + 3)) { //首尾双向缩进
            $stem .= ' <a' . (($cpage - 1) > 0 ? ' href="?' . self::getParam($url_param, $cpage - 1) . '"' : '') . ">{$txpg}</a>";
            $stem .= ' <a href="?' . self::getParam($url_param, 1) . '" >1</a>';
            $stem .= ' <a href="?' . self::getParam($url_param, 2) . '" >2</a>';
            $stem .= ' <span>...</span>';
            for ($i = ($cpage - (ceil($this->pmax / 2) - 1)); $i <= ($cpage + (ceil($this->pmax / 2) - 2)); $i ++) {
                $stem .= ($i == $cpage) ? ' <b>' . $i . '</b>' : ' <a href="?' . self::getParam($url_param, $i) . '" >' . $i . '</a>';
            }
            $stem .= ' <span>...</span>';
            $stem .= ' <a href="?' . self::getParam($url_param, $total - 1) . '" >' . ($total - 1) . '</a>';
            $stem .= ' <a href="?' . self::getParam($url_param, $total) . '" >' . $total . '</a>';
            $stem .= ' <a' . (($cpage + 1) <= $total ? ' href="?' . self::getParam($url_param, $cpage + 1) . '"' : '') . ">{$nxpg}</a>";
        } else { //首部缩进
            $stem .= ' <a' . (($cpage - 1) > 0 ? ' href="?' . self::getParam($url_param, $cpage - 1) . '"' : '') . ">{$txpg}</a>";
            $stem .= ' <a href="?' . self::getParam($url_param, 1) . '" >1</a>';
            $stem .= ' <a href="?' . self::getParam($url_param, 2) . '" >2</a>';
            $stem .= ' <span>...</span>';
            for ($i = ($total - $this->pmax + 1); $i <= $total; $i ++) {
                $stem .= ($i == $cpage) ? ' <b>' . $i . '</b>' : ' <a href="?' . self::getParam($url_param, $i) . '" >' . $i . '</a>';
            }
            $stem .= ' <a' . (($cpage + 1) <= $total ? ' href="?' . self::getParam($url_param, $cpage + 1) . '"' : '') . ">{$nxpg}</a>";
        }
        return $stem;
    }

    /**
     * 取得分页URL连接
     * 
     * @param string $url_param URLparam
     * @param integer $psize 每页显示数
     * @param integer $i 连接增数
     * @return string
     *
     */
    private function getParam(&$url_param, $i) {
        $url_param[$this->pname] = $i;
        return http_build_query($url_param);
    }
}
?>
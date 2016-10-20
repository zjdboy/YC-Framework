<?php

/**
 * YC Framework
 *
 * 缩放图片
 * 保存最佳精度缩放图片
 *
 * @Package YC Framework
 * @Author  zjdboy <zjdboy@163.com>
 * @version $Id: Zoom.php 12222 2013-04-09 05:10:34Z zhengtaoxia $
 **/
class YC_File_Zoom {

    private $_width = 0;
    //实际的宽度
    private $_height = 0;
    //实际的高度
    private $_type = 0;
    //当前图像类型
    static public function Factory() {
        return new self();
    }

    /**
     * 缩放图片
     * 
     * @param str $fname 图像文件实际地址
     * @param int $dw 缩放目标宽
     * @param int $dh 缩放目标高
     * @param str $ext 缩放文件的存在规则
     * @return array
     *
     */
    public function smallExt($fname, $w, $h, $ext) {
        $ext = self::getFileName($fname, $ext);
        self::small($fname, $w, $h, $ext);
    }

    /**
     * 缩放图片
     * 
     * @param str $fname 图像文件实际地址
     * @param int $dw 缩放目标宽
     * @param int $dh 缩放目标高
     * @param str $ext 缩放文件存储地址，为空则不存储
     * @return array
     *
     */
    public function small($fname, $w, $h, $ext = null) {
        //无用的检测，不过针对一些可爱的屌丝还是有必要的
        //if($w<=0 && $h) return false;
        $im = &self::getImgType($fname);
        
        //图片无法被识别
        if (! $im) return false;
        
        //定制图像画布
        $copy = array(
            $w,
            $h,
            0,
            0
        );
        
        //不固定宽
        if ($w == 0) {
            //当宽为0时自适应高
            // if($h>$this->_height){
            //     ImageDestroy($im);
            //     return false;
            // }
            

            //自适应缩放
            $copy[0] = $w = ceil($this->_width * $h / $this->_height);
        } elseif ($h == 0) {
            //自适应宽
            // if($w>$this->_width){
            //     ImageDestroy($im);
            //     return false;
            // }
            

            //自适应缩放
            $copy[1] = $h = ceil($this->_height * $w / $this->_width);
        } else {
            //定宽高缩放
            $copy = self::toWith($w, $h);
        }
        
        //如果需求图片的宽高都比实际的大，则直接返回，防止头像图片很小，但是请求很大导致的白边
        //暂时恢复之前的，后续再处理
        if ($copy[0] >= $this->_width && $copy[1] >= $this->_height) {
            //return readfile($fname);
        }
        
        $newim = imagecreatetruecolor($w, $h);
        
        //加入背景
        // if($this->_width<=$copy[0] || $this->_height<=$copy[1]){
        $back = imagecolorallocate($newim, 255, 255, 255);
        imagefilledrectangle($newim, 0, 0, $w, $h, $back);
        // }
        

        imagecopyresampled($newim, $im, $copy[2], $copy[3], 0, 0, $copy[0], $copy[1], $this->_width, $this->_height);
        ImageDestroy($im); //销毁临时图象释放内存
        return self::outPutImg($newim, $fname, $ext);
    }

    /**
     * 计算图像的可缩放的宽高
     * 
     * @param int $dw 缩放目标宽
     * @param int $dh 缩放目标高
     * @return array
     *
     */
    private function toWith($dw, $dh) {
        $_tmp = array(
            0 => 0,
            1 => 0,
            2 => 0,
            3 => 0
        );
        if ($this->_width <= $dw && $this->_height <= $dh) {
            $w = $this->_width;
            $h = $this->_height;
            $_tmp[2] = ($w < $dw) ? ceil(($dw - $w) / 2) : 0;
            $_tmp[3] = ($h < $dh) ? ceil(($dh - $h) / 2) : 0;
        } else {
            //保持图象最佳效果
            $w = ceil($dh * $this->_width / $this->_height);
            $w = ($w >= $dw - 1 && $w <= $dw + 1) ? $dw : $w;
            if ($w < $dw) {
                $h = ceil($dw * $this->_height / $this->_width);
                $h = ($h >= $dh - 1 && $h <= $dh + 1) ? $dh : $h;
                $w = $dw;
                $_tmp[3] = ceil(($dh - $h) / 2);
            } else {
                $h = ceil($w * $this->_height / $this->_width);
                $h = ($h >= $dh - 1 && $h <= $dh + 1) ? $dh : $h;
                $_tmp[2] = ceil(($dw - $w) / 2);
            }
        }
        $_tmp[0] = $w;
        $_tmp[1] = $h;
        return $_tmp;
    }

    /**
     * 建立同类型临时底图
     * 取得源图片的宽高
     * 
     * @param int $dw 缩放目标宽
     * @param int $dh 缩放目标高
     * @return array
     *
     */
    private function &getImgType($fpath) {
        $n = getimagesize($fpath); //取得图象信息
        $this->_width = $n[0]; //取得图象实际宽度
        $this->_height = $n[1]; //取得图象实际高度
        $this->_type = $n[2];
        
        //根据实际类型创建临时图象
        switch ($this->_type) {
            case 1:
                $tmp = imagecreatefromgif($fpath);
                break;
            case 2:
                $tmp = imagecreatefromjpeg($fpath);
                break;
            case 3:
                $tmp = imagecreatefrompng($fpath);
                break;
            default:
                return false;
        }
        return $tmp;
    }

    /**
     * 基于图像类型送出图像
     * 
     * @param int $dw 缩放目标宽
     * @param int $dh 缩放目标高
     * @return array
     *
     */
    private function outPutImg(&$newim, &$fname, &$ext) {
        if (empty($ext)) {
            $fnew = null;
        } else {
            $fnew = $ext;
        }
        
        switch ($this->_type) {
            case 1:
                imagegif($newim, $fnew);
                ImageDestroy($newim);
                break;
            case 2:
                imagejpeg($newim, $fnew, 90);
                ImageDestroy($newim);
                break;
            case 3:
                imagepng($newim, $fnew);
                ImageDestroy($newim);
                break;
            default:
                return false;
        }
    }

    /**
     * 获取生成图片的地址
     * 
     * @param str $fpath 实际图片地址
     * @param str $ext 新地址参数
     * @return str
     *
     */
    private function getFileName($fpath, $ext) {
        $fpath = substr_replace($fpath, $ext . '.', strrpos($fpath, '.'), 1);
        return $fpath;
    }
}
?>
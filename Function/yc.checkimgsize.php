<?php

/**
 * 获取图片的宽，高，色彩
 *
 * @param string $url 图片的地址
 * @return array()
 **/
function checkimgsize($url = null, $type = 0) {
    $img_dim = array('w' => 0,'h' => 0);
    if ($type == 1) {
        $range = 500000;
        $headers = array('Range: bytes=0-' . $range);
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($curl);
        curl_close($curl);
    } else {
        $data = @file_get_contents($url);
    }
    $data = YC_Func::factory()->xmltoarray($data);
    if (is_array($data) || empty($data)) {
        return $img_dim;
    }
    $img = imagecreatefromstring($data);
    $img_dim['c'] = imagecolorstotal($img);
    $img_dim['w'] = imagesx($img);
    $img_dim['h'] = imagesy($img);
    imagedestroy($img);
    return $img_dim;
}
?>
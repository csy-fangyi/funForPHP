<?php

class Helper
{
    /**
     * 判断当前服务器系统
     * @return string
     */
    public static function getOS() {
        if (PATH_SEPARATOR == ':') {
            return 'Linux';
        } else {
            return 'Windows';
        }
    }

    /**
     * 返回二维数组中某个键名的所有值
     * @param array $array
     * @param string $key
     * @return array
     */
    public static function array_key_values($array = [], $key = '') {
        $ret = [];
        foreach ((array)$array as $k => $v) {
            $ret[$k] = $v[$key];
        }
        return $ret;
    }

    /**
     * 格式化单位
     */
    public static function byteFormat($size, $dec = 2) {
        $a = ["B", "KB", "MB", "GB", "TB", "PB"];
        $pos = 0;
        while ($size >= 1024) {
            $size /= 1024;
            $pos++;
        }
        return round($size, $dec) . " " . $a[$pos];
    }

    /**
     * 下拉框，单选按钮 自动选择
     *
     * @param $string 输入字符
     * @param $param  条件
     * @param $type   类型
     * selected checked
     * @return string
     */
    public static function selected($string, $param = 1, $type = 'select') {
        $true = false;
        if (is_array($param)) {
            $true = in_array($string, $param);
        } else if ($string == $param) {
            $true = true;
        }
        $return = '';
        if ($true) $return = $type == 'select' ? 'selected="selected"' : 'checked="checked"';

        echo $return;
    }

    /**
     * 下载远程图片
     * @param string $url 图片的绝对url
     * @param string $filepath 文件的完整路径（例如/www/images/test） ，此函数会自动根据图片url和http头信息确定图片的后缀名
     * @param string $filename 要保存的文件名(不含扩展名)
     * @return mixed 下载成功返回一个描述图片信息的数组，下载失败则返回false
     */
    public static function downloadImage($url, $filepath, $filename) {
//服务器返回的头信息
        $responseHeaders = [];
//原始图片名
        $originalfilename = '';
//图片的后缀名
        $ext = '';
        $ch = curl_init($url);
//设置curl_exec返回的值包含Http头
        curl_setopt($ch, CURLOPT_HEADER, 1);
//设置curl_exec返回的值包含Http内容
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//设置抓取跳转（http 301，302）后的页面
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
//设置最多的HTTP重定向的数量
        curl_setopt($ch, CURLOPT_MAXREDIRS, 3);

//服务器返回的数据（包括http头信息和内容）
        $html = curl_exec($ch);
//获取此次抓取的相关信息
        $httpinfo = curl_getinfo($ch);
        curl_close($ch);
        if ($html !== false) {
//分离response的header和body，由于服务器可能使用了302跳转，所以此处需要将字符串分离为 2+跳转次数 个子串
            $httpArr = explode("\r\n\r\n", $html, 2 + $httpinfo['redirect_count']);
//倒数第二段是服务器最后一次response的http头
            $header = $httpArr[count($httpArr) - 2];
//倒数第一段是服务器最后一次response的内容
            $body = $httpArr[count($httpArr) - 1];
            $header .= "\r\n";

//获取最后一次response的header信息
            preg_match_all('/([a-z0-9-_]+):\s*([^\r\n]+)\r\n/i', $header, $matches);
            if (!empty($matches) && count($matches) == 3 && !empty($matches[1]) && !empty($matches[1])) {
                for ($i = 0; $i < count($matches[1]); $i++) {
                    if (array_key_exists($i, $matches[2])) {
                        $responseHeaders[$matches[1][$i]] = $matches[2][$i];
                    }
                }
            }
//获取图片后缀名
            if (0 < preg_match('{(?:[^\/\\\\]+)\.(jpg|jpeg|gif|png|bmp)$}i', $url, $matches)) {
                $originalfilename = $matches[0];
                $ext = $matches[1];
            } else {
                if (array_key_exists('Content-Type', $responseHeaders)) {
                    if (0 < preg_match('{image/(\w+)}i', $responseHeaders['Content-Type'], $extmatches)) {
                        $ext = $extmatches[1];
                    }
                }
            }
//保存文件
            if (!empty($ext)) {
//如果目录不存在，则先要创建目录
                if (!is_dir($filepath)) {
                    mkdir($filepath, 0777, true);
                }

                $filepath .= '/' . $filename . ".$ext";
                $local_file = fopen($filepath, 'w');
                if (false !== $local_file) {
                    if (false !== fwrite($local_file, $body)) {
                        fclose($local_file);
                        $sizeinfo = getimagesize($filepath);
                        return ['filepath' => realpath($filepath), 'width' => $sizeinfo[0], 'height' => $sizeinfo[1], 'orginalfilename' => $originalfilename, 'filename' => pathinfo($filepath, PATHINFO_BASENAME)];
                    }
                }
            }
        }
        return false;
    }

    /**
     * 查找ip是否在某个段位里面
     * @param string $ip 要查询的ip
     * @param $arrIP     禁止的ip
     * @return boolean
     */
    public static function ipAccess($ip = '0.0.0.0', $arrIP = []) {
        $access = true;
        $ip && $arr_cur_ip = explode('.', $ip);
        foreach ((array)$arrIP as $key => $value) {
            if ($value == '*.*.*.*') {
                $access = false; //禁止所有
                break;
            }
            $tmp_arr = explode('.', $value);
            if (($arr_cur_ip[0] == $tmp_arr[0]) && ($arr_cur_ip[1] == $tmp_arr[1])) {
//前两段相同
                if (($arr_cur_ip[2] == $tmp_arr[2]) || ($tmp_arr[2] == '*')) {
//第三段为* 或者相同
                    if (($arr_cur_ip[3] == $tmp_arr[3]) || ($tmp_arr[3] == '*')) {
//第四段为* 或者相同
                        $access = false; //在禁止ip列，则禁止访问
                        break;
                    }
                }
            }
        }
        return $access;
    }

    /**
     * @param string $string 原文或者密文
     * @param string $operation 操作(ENCODE | DECODE), 默认为 DECODE
     * @param string $key 密钥
     * @param int $expiry 密文有效期, 加密时候有效， 单位 秒，0 为永久有效
     * @return string 处理后的 原文或者 经过 base64_encode 处理后的密文
     *
     * @example
     *
     * $a = authcode('abc', 'ENCODE', 'key');
     * $b = authcode($a, 'DECODE', 'key');  // $b(abc)
     *
     * $a = authcode('abc', 'ENCODE', 'key', 3600);
     * $b = authcode('abc', 'DECODE', 'key'); // 在一个小时内，$b(abc)，否则 $b 为空
     */
    public static function authcode($string, $operation = 'DECODE', $key = '', $expiry = 3600) {
        $ckey_length = 4;
// 随机密钥长度 取值 0-32;
// 加入随机密钥，可以令密文无任何规律，即便是原文和密钥完全相同，加密结果也会每次不同，增大po,jie难度。
// 取值越大，密文变动规律越大，密文变化 = 16 的 $ckey_length 次方
// 当此值为 0 时，则不产生随机密钥

        $key = md5($key ? $key : 'key'); //这里可以填写默认key值
        $keya = md5(substr($key, 0, 16));
        $keyb = md5(substr($key, 16, 16));
        $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';

        $cryptkey = $keya . md5($keya . $keyc);
        $key_length = strlen($cryptkey);

        $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
        $string_length = strlen($string);

        $result = '';
        $box = range(0, 255);

        $rndkey = [];
        for ($i = 0; $i <= 255; $i++) {
            $rndkey [$i] = ord($cryptkey [$i % $key_length]);
        }

        for ($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box [$i] + $rndkey [$i]) % 256;
            $tmp = $box [$i];
            $box [$i] = $box [$j];
            $box [$j] = $tmp;
        }

        for ($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box [$a]) % 256;
            $tmp = $box [$a];
            $box [$a] = $box [$j];
            $box [$j] = $tmp;
            $result .= chr(ord($string [$i]) ^ ($box [($box [$a] + $box [$j]) % 256]));
        }

        if ($operation == 'DECODE') {
            if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            return $keyc . str_replace('=', '', base64_encode($result));
        }
    }

    /**
     * js 弹窗并且跳转
     * @param string $_info
     * @param string $_url
     * @return js
     */
    public static function alertLocation($_info, $_url) {
        echo "<script type='text/javascript'>alert('$_info');location.href='$_url';</script>";
        exit();
    }

    /**
     * js 弹窗返回
     * @param string $_info
     * @return js
     */
    public static function alertBack($_info) {
        echo "<script type='text/javascript'>alert('$_info');history.back();</script>";
        exit();
    }

    /**
     * 页面跳转
     * @param string $url
     * @return js
     */
    public static function headerUrl($url) {
        echo "<script type='text/javascript'>location.href='{$url}';</script>";
        exit();
    }

    /**
     * 弹窗关闭
     * @param string $_info
     * @return js
     */
    public static function alertClose($_info) {
        echo "<script type='text/javascript'>alert('$_info');close();</script>";
        exit();
    }

    /**
     * 弹窗
     * @param string $_info
     * @return js
     */
    public static function alert($_info) {
        echo "<script type='text/javascript'>alert('$_info');</script>";
        exit();
    }

    /**
     * 系统基本参数上传图片专用
     * @param string $_path
     * @return null
     */
    public static function sysUploadImg($_path) {
        echo '<script type="text/javascript">document.getElementById("logo").value="' . $_path . '";</script>';
        echo '<script type="text/javascript">document.getElementById("pic").src="' . $_path . '";</script>';
        echo '<script type="text/javascript">$("#loginpop1").hide();</script>';
        echo '<script type="text/javascript">$("#bgloginpop2").hide();</script>';
    }

    /**
     * html过滤
     * @param array|object $_date
     * @return string
     */
    public static function htmlString($_date) {
        if (is_array($_date)) {
            $_string = [];
            foreach ($_date as $_key => $_value) {
                $_string[$_key] = self::htmlString($_value);  //递归
            }
        } else if (is_object($_date)) {
            $_string = [];
            foreach ($_date as $_key => $_value) {
                $_string->$_key = self::htmlString($_value);  //递归
            }
        } else {
            $_string = htmlspecialchars($_date);
        }
        return $_string;
    }

    /**
     * 数据库输入过滤
     * @param string $_data
     * @return string
     */
    public static function mysqlString($_data) {
        $_data = trim($_data);
        return !GPC ? addcslashes($_data) : $_data;
    }

    /**
     * 清理session
     */
    public static function unSession() {
        if (session_start()) {
            session_destroy();
        }
    }

    /**
     * 验证是否为空
     * @param string $str
     * @param string $name
     * @return bool (true or false)
     */
    static function validateEmpty($str, $name) {
        if (empty($str)) {
            self::alertBack('警告：' . $name . '不能为空！');
        }
    }

    /**
     * 验证是否相同
     * @param string $str1
     * @param string $str2
     * @param string $alert
     * @return JS
     */
    static function validateAll($str1, $str2, $alert) {
        if ($str1 != $str2) self::alertBack('警告：' . $alert);
    }

    /**
     * 验证ID
     * @param Number $id
     * @return JS
     */
    static function validateId($id) {
        if (empty($id) || !is_numeric($id)) self::alertBack('警告：参数错误！');
    }

    /**
     * 获得真实IP地址
     * @return string
     */
    public static function realIp() {
        static $realip = null;
        if ($realip !== null) return $realip;
        if (isset($_SERVER)) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                foreach ($arr AS $ip) {
                    $ip = trim($ip);
                    if ($ip != 'unknown') {
                        $realip = $ip;
                        break;
                    }
                }
            } else if (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $realip = $_SERVER['HTTP_CLIENT_IP'];
            } else {
                if (isset($_SERVER['REMOTE_ADDR'])) {
                    $realip = $_SERVER['REMOTE_ADDR'];
                } else {
                    $realip = '0.0.0.0';
                }
            }
        } else {
            if (getenv('HTTP_X_FORWARDED_FOR')) {
                $realip = getenv('HTTP_X_FORWARDED_FOR');
            } else if (getenv('HTTP_CLIENT_IP')) {
                $realip = getenv('HTTP_CLIENT_IP');
            } else {
                $realip = getenv('REMOTE_ADDR');
            }
        }
        preg_match('/[\d\.]{7,15}/', $realip, $onlineip);
        $realip = !empty($onlineip[0]) ? $onlineip[0] : '0.0.0.0';
        return $realip;
    }

    /**
     * 加载 Smarty 模板
     * @param string $html
     * @return null;
     */
    public static function display() {
        global $tpl;
        $html = null;
        $htmlArr = explode('/', $_SERVER[SCRIPT_NAME]);
        $html = str_ireplace('.php', '.html', $htmlArr[count($htmlArr) - 1]);
        $dir = dirname($_SERVER[SCRIPT_NAME]);
        $firstStr = substr($dir, 0, 1);
        $endStr = substr($dir, strlen($dir) - 1, 1);
        if ($firstStr == '/' || $firstStr == '\\') $dir = substr($dir, 1);
        if ($endStr != '/' || $endStr != '\\') $dir = $dir . '/';
        $tpl->display($dir . $html);
    }

    /**
     * 正确获取变量
     * @param string $param
     * @param string $type
     * @return string
     */
    public static function getData($param, $type = 'post') {
        $type = strtolower($type);
        if ($type == 'post') {
            return self::mysqlString(trim($_POST[$param]));
        } else if ($type == 'get') {
            return self::mysqlString(trim($_GET[$param]));
        }
    }


    /**
     * 验证登陆
     */
    public static function validateLogin() {
        if (empty($_SESSION['admin']['user'])) header('Location:/admin/');
    }

    /**
     * 给已经存在的图片添加水印
     * @param string $file_path
     * @return bool
     */
    public static function addMark($file_path) {
        if (file_exists($file_path) && file_exists(MARK)) {
//求出上传图片的名称后缀
            $ext_name = strtolower(substr($file_path, strrpos($file_path, '.'), strlen($file_path)));
//$new_name='jzy_' . time() . rand(1000,9999) . $ext_name ;
            $store_path = ROOT_PATH . UPDIR;
//求上传图片高宽
            $imginfo = getimagesize($file_path);
            $width = $imginfo[0];
            $height = $imginfo[1];
//添加图片水印
            switch ($ext_name) {
                case '.gif':
                    $dst_im = imagecreatefromgif($file_path);
                    break;
                case '.jpg':
                    $dst_im = imagecreatefromjpeg($file_path);
                    break;
                case '.png':
                    $dst_im = imagecreatefrompng($file_path);
                    break;
            }
            $src_im = imagecreatefrompng(MARK);
//求水印图片高宽
            $src_imginfo = getimagesize(MARK);
            $src_width = $src_imginfo[0];
            $src_height = $src_imginfo[1];
//求出水印图片的实际生成位置
            $src_x = $width - $src_width - 10;
            $src_y = $height - $src_height - 10;
//新建一个真彩色图像
            $nimage = imagecreatetruecolor($width, $height);
//拷贝上传图片到真彩图像
            imagecopy($nimage, $dst_im, 0, 0, 0, 0, $width, $height);
//按坐标位置拷贝水印图片到真彩图像上
            imagecopy($nimage, $src_im, $src_x, $src_y, 0, 0, $src_width, $src_height);
//分情况输出生成后的水印图片
            switch ($ext_name) {
                case '.gif':
                    imagegif($nimage, $file_path);
                    break;
                case '.jpg':
                    imagejpeg($nimage, $file_path);
                    break;
                case '.png':
                    imagepng($nimage, $file_path);
                    break;
            }
//释放资源
            imagedestroy($dst_im);
            imagedestroy($src_im);
            unset($imginfo);
            unset($src_imginfo);
//移动生成后的图片
            @move_uploaded_file($file_path, ROOT_PATH . UPDIR . $file_path);
        }
    }

    /**
     * 删除图片，根据图片ID
     * @param int $image_id
     */
    static function delPicByImageId($image_id) {
        $db_name = PREFIX . 'images i';
        $m = new Model();
        $data = $m->getOne($db_name, "i.id={$image_id}", "i.path as p, i.big_img as b, i.small_img as s");
        foreach ($data as $v) {
            @self::delFile(ROOT_PATH . $v['p']);
            @self::delFile(ROOT_PATH . $v['b']);
            @self::delFile(ROOT_PATH . $v['s']);
        }
        $m->del(PREFIX . 'images', "id={$image_id}");
        unset($m);
    }

    /**
     * 图片等比例缩放
     * @param resource $im 新建图片资源(imagecreatefromjpeg/imagecreatefrompng/imagecreatefromgif)
     * @param int $maxwidth 生成图像宽
     * @param int $maxheight 生成图像高
     * @param string $name 生成图像名称
     * @param string $filetype文件类型 (.jpg/.gif/.png)
     */
    public static function resizeImage($im, $maxwidth, $maxheight, $name, $filetype) {
        $pic_width = imagesx($im);
        $pic_height = imagesy($im);
        if (($maxwidth && $pic_width > $maxwidth) || ($maxheight && $pic_height > $maxheight)) {
            if ($maxwidth && $pic_width > $maxwidth) {
                $widthratio = $maxwidth / $pic_width;
                $resizewidth_tag = true;
            }
            if ($maxheight && $pic_height > $maxheight) {
                $heightratio = $maxheight / $pic_height;
                $resizeheight_tag = true;
            }
            if ($resizewidth_tag && $resizeheight_tag) {
                if ($widthratio < $heightratio) $ratio = $widthratio; else
                    $ratio = $heightratio;
            }
            if ($resizewidth_tag && !$resizeheight_tag) $ratio = $widthratio;
            if ($resizeheight_tag && !$resizewidth_tag) $ratio = $heightratio;
            $newwidth = $pic_width * $ratio;
            $newheight = $pic_height * $ratio;
            if (function_exists("imagecopyresampled")) {
                $newim = imagecreatetruecolor($newwidth, $newheight);
                imagecopyresampled($newim, $im, 0, 0, 0, 0, $newwidth, $newheight, $pic_width, $pic_height);
            } else {
                $newim = imagecreate($newwidth, $newheight);
                imagecopyresized($newim, $im, 0, 0, 0, 0, $newwidth, $newheight, $pic_width, $pic_height);
            }
            $name = $name . $filetype;
            imagejpeg($newim, $name);
            imagedestroy($newim);
        } else {
            $name = $name . $filetype;
            imagejpeg($im, $name);
        }
    }

    /**
     * 下载文件
     * @param string $file_path 绝对路径
     */
    public static function downFile($file_path) {
//判断文件是否存在
        $file_path = iconv('utf-8', 'gb2312', $file_path); //对可能出现的中文名称进行转码
        if (!file_exists($file_path)) {
            exit('文件不存在！');
        }
        $file_name = basename($file_path); //获取文件名称
        $file_size = filesize($file_path); //获取文件大小
        $fp = fopen($file_path, 'r'); //以只读的方式打开文件
        header("Content-type: application/octet-stream");
        header("Accept-Ranges: bytes");
        header("Accept-Length: {$file_size}");
        header("Content-Disposition: attachment;filename={$file_name}");
        $buffer = 1024;
        $file_count = 0;
//判断文件是否结束
        while (!feof($fp) && ($file_size - $file_count > 0)) {
            $file_data = fread($fp, $buffer);
            $file_count += $buffer;
            echo $file_data;
        }
        fclose($fp); //关闭文件
    }
}

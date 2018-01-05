<?php

namespace Helper;

class StringHelper
{
    /**
     * 格式化字符串
     * @param string $str
     * @return string
     */
    static public function formatStr($str) {
        $arr = [' ', ' ', '&', '@', '#', '%', '\'', '"', '\\', '/', '.', ',', '$', '^', '*', '(', ')', '[', ']', '{', '}', '|', '~', '`', '?', '!', ';', ':', '-', '_', '+', '='];
        foreach ($arr as $v) {
            $str = str_replace($v, '', $str);
        }
        return $str;
    }

    public static function gbkToUtf8($str) {
        return iconv("GBK", "UTF-8", $str);
    }

    /**
     * 切割utf-8格式的字符串(一个汉字或者字符占一个字节)
     * @param $string
     * @param $length
     * @param string $etc
     * @return string
     */
    public static function truncate_utf8_string($string, $length, $etc = '...') {
        $result = '';
        $string = html_entity_decode(trim(strip_tags($string)), ENT_QUOTES, 'UTF-8');
        $strLen = strlen($string);
        for ($i = 0; (($i < $strLen) && ($length > 0)); $i++) {
            if ($number = strpos(str_pad(decbin(ord(substr($string, $i, 1))), 8, '0', STR_PAD_LEFT), '0')) {
                if ($length < 1.0) {
                    break;
                }
                $result .= substr($string, $i, $number);
                $length -= 1.0;
                $i += $number - 1;
            } else {
                $result .= substr($string, $i, 1);
                $length -= 0.5;
            }
        }
        $result = htmlspecialchars($result, ENT_QUOTES, 'UTF-8');
        if ($i < $strLen) {
            $result .= $etc;
        }
        return $result;
    }

    /**
     * 判断字符串是utf-8 还是gb2312
     * @param string $str
     * @param string $default
     * @return string
     */
    public static function utf8_gb2312($str, $default = 'gb2312') {
        $str = preg_replace("/[\x01-\x7F]+/", "", $str);
        if (empty($str)) {
            return $default;
        }

        $preg = ["gb2312" => "/^([\xA1-\xF7][\xA0-\xFE])+$/", //正则判断是否是gb2312
                 "utf-8"  => "/^[\x{4E00}-\x{9FA5}]+$/u",      //正则判断是否是汉字(utf8编码的条件了)，这个范围实际上已经包含了繁体中文字了
        ];

        if ($default == 'gb2312') {
            $option = 'utf-8';
        } else {
            $option = 'gb2312';
        }

        if (!preg_match($preg[$default], $str)) {
            return $option;
        }
        $str = @iconv($default, $option, $str);

        //不能转成 $option, 说明原来的不是 $default
        if (empty($str)) {
            return $option;
        }
        return $default;
    }

    /**
     * utf-8和gb2312自动转化
     * @param string $string
     * @param string $outEncoding
     * @return string
     */
    public static function safeEncoding($string, $outEncoding = 'UTF-8') {
        $encoding = "UTF-8";
        for ($i = 0; $i < strlen($string); $i++) {
            if (ord($string{$i}) < 128) {
                continue;
            }

            if ((ord($string{$i}) & 224) == 224) {
                // 第一个字节判断通过
                $char = $string{++$i};
                if ((ord($char) & 128) == 128) {
                    // 第二个字节判断通过
                    $char = $string{++$i};
                    if ((ord($char) & 128) == 128) {
                        $encoding = "UTF-8";
                        break;
                    }
                }
            }
            if ((ord($string{$i}) & 192) == 192) {
                // 第一个字节判断通过
                $char = $string{++$i};
                if ((ord($char) & 128) == 128) {
                    // 第二个字节判断通过
                    $encoding = "GB2312";
                    break;
                }
            }
        }

        if (strtoupper($encoding) == strtoupper($outEncoding)) {
            return $string;
        } else {
            return @iconv($encoding, $outEncoding, $string);
        }
    }

    /**
     *  中文截取2，单字节截取模式
     * @access public
     * @param string $str 需要截取的字符串
     * @param int $slen 截取的长度
     * @param int $startdd 开始标记处
     * @return string
     */
    const PAGECHARSET = 'utf-8';

    public static function cn_substr($str, $slen, $startdd = 0) {
        $cfg_soft_lang = self::PAGECHARSET;
        if ($cfg_soft_lang == 'utf-8') {
            return self::cn_substr_utf8($str, $slen, $startdd);
        }
        $restr = '';
        $c = '';
        $str_len = strlen($str);
        if ($str_len < $startdd + 1) {
            return '';
        }
        if ($str_len < $startdd + $slen || $slen == 0) {
            $slen = $str_len - $startdd;
        }
        $enddd = $startdd + $slen - 1;
        for ($i = 0; $i < $str_len; $i++) {
            if ($startdd == 0) {
                $restr .= $c;
            } else {
                if ($i > $startdd) {
                    $restr .= $c;
                }
            }
            if (ord($str[$i]) > 0x80) {
                if ($str_len > $i + 1) {
                    $c = $str[$i] . $str[$i + 1];
                }
                $i++;
            } else {
                $c = $str[$i];
            }
            if ($i >= $enddd) {
                if (strlen($restr) + strlen($c) > $slen) {
                    break;
                } else {
                    $restr .= $c;
                    break;
                }
            }
        }
        return $restr;
    }

    /**
     *  utf-8中文截取，单字节截取模式
     *
     * @access public
     * @param string $str 需要截取的字符串
     * @param int $length 截取的长度
     * @param int $start 开始标记处
     * @return string
     */
    public static function cn_substr_utf8($str, $length, $start = 0) {
        if (strlen($str) < $start + 1) {
            return '';
        }
        preg_match_all("/./su", $str, $ar);
        $str = '';
        $tstr = '';
        //为了兼容mysql4.1以下版本,与数据库varchar一致,这里使用按字节截取
        for ($i = 0; isset($ar[0][$i]); $i++) {
            if (strlen($tstr) < $start) {
                $tstr .= $ar[0][$i];
            } else {
                if (strlen($str) < $length + strlen($ar[0][$i])) {
                    $str .= $ar[0][$i];
                } else {
                    break;
                }
            }
        }
        return $str;
    }
}
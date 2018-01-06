<?php

namespace Helper;

class ValidatorHelper
{
    //正则验证手机号 正确返回 true
    function preg_mobile($mobile) {
        if (preg_match("/^1[34578]\d{9}$/", $mobile)) {
            return true;
        } else {
            return false;
        }
    }

    //验证电话号码
    function preg_tel($tel) {
        if (preg_match("/^(\(\d{3,4}\)|\d{3,4}-)?\d{7,8}$/", $tel)) {
            return true;
        } else {
            return false;
        }
    }

    //验证身份证号（15位或18位数字）
    function preg_idcard($idcard) {
        if (preg_match("/^\d{15}|\d{18}$/", $idcard)) {
            return true;
        } else {
            return false;
        }
    }

    //验证是否是数字(这里小数点会认为是字符)
    function preg_digit($digit) {
        if (preg_match("/^\d*$/", $digit)) {
            return true;
        } else {
            return false;
        }
    }

    //验证是否是数字(可带小数点的数字)
    function preg_num($num) {
        if (is_numeric($num)) {
            return true;
        } else {
            return false;
        }
    }

    //验证由数字、26个英文字母或者下划线组成的字符串
    function preg_str($str) {
        if (preg_match("/^\w+$/", $str)) {
            return true;
        } else {
            return false;
        }
    }

    //验证用户密码(以字母开头，长度在6-18之间，只能包含字符、数字和下划线)
    function preg_password($str) {
        if (preg_match("/^[a-zA-Z]\w{5,17}$/", $str)) {
            return true;
        } else {
            return false;
        }
    }

    //验证汉字
    function preg_chinese($str) {
        if (preg_match("/^[\u4e00-\u9fa5],{0,}$/", $str)) {
            return true;
        } else {
            return false;
        }
    }

    //验证Email地址
    function preg_email($email) {
        if (preg_match("/^\w+[-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/", $email)) {
            return true;
        } else {
            return false;
        }
    }

    //验证网址URL
    function preg_link($url) {
        if (preg_match("/http:\/\/[\w.]+[\w\/]*[\w.]*\??[\w=&\+\%]*/is", $url)) {
            return true;
        } else {
            return false;
        }
    }

    //腾讯QQ号
    function preg_qq($qq) {
        if (preg_match("/^[1-9][0-9]{4,}$/", $qq)) {
            return true;
        } else {
            return false;
        }
    }

    //验证中国邮政编码 6位数字
    function preg_post($post) {
        if (preg_match("/^[1-9]\d{5}(?!\d)$/", $post)) {
            return true;
        } else {
            return false;
        }
    }

    //验证IP地址
    function preg_ip($ip) {
        if (preg_match("/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/", $ip)) {
            return true;
        } else {
            return false;
        }
    }
}
<?php

namespace Helper;

class TimeHelper
{
    /**
     * 当前微妙数
     * @return number
     */
    public static function microTimeFloat() {
        list ($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }

    /**
     * 格式化时间
     * @param string $time 时间戳
     * @return string
     */
    static public function formatDate($time = 'default') {
        return $time == 'default' ? date('Y-m-d H:i:s', time()) : date('Y-m-d H:i:s', $time);
    }

}
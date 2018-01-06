<?php

//二分查找
function bin_sch($array, $low, $high, $k) {
    if ($low <= $high) {
        $mid = intval(($low + $high) / 2);
        if ($array[$mid] == $k) {
            return $mid;
        } else {
            if ($k < $array[$mid]) {
                return bin_sch($array, $low, $mid - 1, $k);
            } else {
                return bin_sch($array, $mid + 1, $high, $k);
            }
        }
    }
    return -1;
}

//顺序查找（数组里查找某个元素）
function seq_sch($array, $n, $k) {
    $array[$n] = $k;
    for ($i = 0; $i < $n; $i++) {
        if ($array[$i] == $k) {
            break;
        }
    }
    if ($i < $n) {
        return $i;
    } else {
        return -1;
    }
}

//线性表的删除（数组中实现）
function delete_array_element($array, $i) {
    $len = count($array);
    for ($j = $i; $j < $len; $j++) {
        $array[$j] = $array[$j + 1];
    }
    array_pop($array);
    return $array;
}

//冒泡排序（数组排序）
function bubble_sort($array) {
    $count = count($array);
    if ($count <= 0) {
        return false;
    }
    for ($i = 0; $i < $count; $i++) {
        for ($j = $count - 1; $j > $i; $j--) {
            if ($array[$j] < $array[$j - 1]) {
                $tmp = $array[$j];
                $array[$j] = $array[$j - 1];
                $array[$j - 1] = $tmp;
            }
        }
    }
    return $array;
}

//快速排序（数组排序）
function quick_sort($array) {
    if (count($array) <= 1) {
        return $array;
    }
    $key = $array[0];
    $left_arr = [];
    $right_arr = [];
    for ($i = 1; $i < count($array); $i++) {
        if ($array[$i] <= $key) {
            $left_arr[] = $array[$i];
        } else {
            $right_arr[] = $array[$i];
        }
    }

    $left_arr = quick_sort($left_arr);
    $right_arr = quick_sort($right_arr);
    return array_merge($left_arr, [$key], $right_arr);
}

//获得文件属性 $file是文件路径如$_SERVER['SCRIPT_FILENAME'],$flag文件的某个属性
function getFileAttr($file, $flag) {
    if (!file_exists($file)) {
        return false;
    }
    switch ($flag) {
        case 'dir':
            if (is_file($file)) {
                return dirname($file);
            }
            return realpath($file);
            break;
        case 'name':
            if (is_file($file)) {
                return basename($file);
            }
            return '-';
            break;
        case 'size':
            if (is_file($file)) {
                return filesize($file);
            }
            return '-';
            break;
        case 'perms':
            return substr(sprintf('%o', fileperms($file)), -4);;
            break;
        case 'ower':
            return fileowner($file);
            break;
        case 'owername':
            $ownerInfo = posix_getpwuid(fileowner($file));
            return isset($ownerInfo['name']) ? $ownerInfo['name'] : false;
            break;
        case 'groupname':
            $ownerInfo = posix_getpwuid(filegroup($file));
            return isset($ownerInfo['name']) ? $ownerInfo['name'] : false;
            break;
        case 'ctime':
            return filectime($file);
            break;
        case 'mtime':
            return filemtime($file);
            break;
        case 'atime':
            return fileatime($file);
            break;
        case 'suffix':
            if (is_file($file)) {
                return substr($file, strrpos($file, '.') + 1);
            }
            return '-';
            break;
        default:
            return false;
            break;
    }
}

//去除数组中的单斜线
function stripslashes_deep($value) {
    $value = is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
    return $value;
}

//入库添加斜线 防sql注入
function add_slashes_recursive($variable) {
    if (is_string($variable)) {
        return addslashes($variable);
    } else {
        if (is_array($variable)) {
            foreach ($variable as $i => $value) $variable[$i] = add_slashes_recursive($value);
        }
    }

    return $variable;
}

//页面显示时去掉数据库中数据的斜线
function strip_slashes_recursive($variable) {
    if (is_string($variable)) {
        return stripslashes($variable);
    }
    if (is_array($variable)) {
        foreach ($variable as $i => $value) $variable[$i] = strip_slashes_recursive($value);
    }

    return $variable;
}


/**
 * 根据数据中的某一字段排序
 * @param array $array 原始数组
 * @param $field 数组字段
 * @param bool|false $desc
 */
function sortArrByField(&$array, $field, $desc = true) {
    $fieldArr = [];
    foreach ($array as $k => $v) {
        $fieldArr[$k] = $v[$field];
    }
    $sort = $desc ? SORT_DESC : SORT_ASC;
    array_multisort($fieldArr, $sort, $array);
}

//获得数组中重复的数据
function fetchRepeatMemberInArray($array) {
    // 获取去掉重复数据的数组
    $unique_arr = array_unique($array);
    // 获取重复数据的数组
    $repeat_arr = array_diff_assoc($array, $unique_arr);
    return $repeat_arr;
}

//PHP实现双端队列
class Deque
{
    public $queue = [];

    /**（尾部）入队  **/
    public function addLast($value) {
        return array_push($this->queue, $value);
    }

    /**（尾部）出队**/
    public function removeLast() {
        return array_pop($this->queue);
    }

    /**（头部）入队**/
    public function addFirst($value) {
        return array_unshift($this->queue, $value);
    }

    /**（头部）出队**/
    public function removeFirst() {
        return array_shift($this->queue);
    }

    /**清空队列**/
    public function makeEmpty() {
        unset($this->queue);
    }

    /**获取列头**/
    public function getFirst() {
        return reset($this->queue);
    }

    /** 获取列尾 **/
    public function getLast() {
        return end($this->queue);
    }

    /** 获取长度 **/
    public function getLength() {
        return count($this->queue);
    }

}
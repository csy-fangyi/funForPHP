<?php

namespace Helper;

class FileHelper
{
    /**
     * 创建目录
     * @param string $dir
     */
    public static function createDir($dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0777);
        }
    }

    /**
     * 删除目录
     * @param string $path
     */
    public static function delDir($path) {
        if (is_dir($path)) {
            rmdir($path);
        }
    }

    /**
     * 创建文件（默认为空）
     * @param string $filename
     */
    public static function createFile($filename) {
        if (!is_file($filename)) {
            touch($filename);
        }
    }

    /**
     * 删除文件
     * @param string $filename
     */
    public static function delFile($filename) {
        if (file_exists($filename)) {
            unlink($filename);
        }
    }

    /**
     * 删除目录及地下的全部文件
     * @param string $dir
     * @return bool
     */
    public static function delDirOfAll($dir) {
        if (!is_dir($dir)) {
            return true;
        }
        //先删除目录下的文件：
        $dh = opendir($dir);
        while (!!$file = readdir($dh)) {
            if ($file != "." && $file != "..") {
                $fullpath = $dir . "/" . $file;
                if (!is_dir($fullpath)) {
                    unlink($fullpath);
                } else {
                    self::delDirOfAll($fullpath);
                }
            }
        }
        closedir($dh);
        //删除当前文件夹
        return rmdir($dir);
    }

    /**
     * 遍历文件夹
     * @param string $dir
     * @param boolean $all true表示递归遍历
     * @param $ret
     * @return array
     */
    public static function scanfDir($dir = '', $all = false, &$ret = []) {
        if (false !== ($handle = opendir($dir))) {
            while (false !== ($file = readdir($handle))) {
                if (!in_array($file, ['.', '..', '.git', '.gitignore', '.svn', '.htaccess', '.buildpath', '.project'])) {
                    $cur_path = $dir . '/' . $file;
                    if (is_dir($cur_path)) {
                        $ret['dirs'][] = $cur_path;
                        $all && self::scanfDir($cur_path, $all, $ret);
                    } else {
                        $ret ['files'] [] = $cur_path;
                    }
                }
            }
            closedir($handle);
        }
        return $ret;
    }

    /**
     * 判断 文件/目录 是否可写（取代系统自带的 is_writeable 函数）
     * @param string $file 文件/目录
     * @return boolean
     */
    public static function is_writeable($file) {
        if (is_dir($file)) {
            $dir = $file;
            if ($fp = @fopen("$dir/test.txt", 'w')) {
                @fclose($fp);
                @unlink("$dir/test.txt");
                $writeable = 1;
            } else {
                $writeable = 0;
            }
        } else {
            if ($fp = @fopen($file, 'a+')) {
                @fclose($fp);
                $writeable = 1;
            } else {
                $writeable = 0;
            }
        }

        return $writeable;
    }

    /**
     * 取得输入目录所包含的所有目录和文件
     * 以关联数组形式返回
     * author: flynetcn
     */
    public static function deepScanDir($dir) {
        $fileArr = [];
        $dirArr = [];
        $dir = rtrim($dir, '//');
        if (is_dir($dir)) {
            $dirHandle = opendir($dir);
            while (false !== ($fileName = readdir($dirHandle))) {
                $subFile = $dir . DIRECTORY_SEPARATOR . $fileName;
                if (is_file($subFile)) {
                    $fileArr[] = $subFile;
                } else {
                    if (is_dir($subFile) && str_replace('.', '', $fileName) != '') {
                        $dirArr[] = $subFile;
                        $arr = self::deepScanDir($subFile);
                        $dirArr = array_merge($dirArr, $arr['dir']);
                        $fileArr = array_merge($fileArr, $arr['file']);
                    }
                }
            }
            closedir($dirHandle);
        }
        return ['dir' => $dirArr, 'file' => $fileArr];
    }

    /**
     * 取得输入目录所包含的所有文件
     * 以数组形式返回
     * author: flynetcn
     */
    public static function get_dir_files($dir) {
        if (is_file($dir)) {
            return [$dir];
        }
        $files = [];
        if (is_dir($dir) && ($dir_p = opendir($dir))) {
            $ds = DIRECTORY_SEPARATOR;
            while (($filename = readdir($dir_p)) !== false) {
                if ($filename == '.' || $filename == '..') {
                    continue;
                }
                $filetype = filetype($dir . $ds . $filename);
                if ($filetype == 'dir') {
                    $files = array_merge($files, self::get_dir_files($dir . $ds . $filename));
                } else {
                    if ($filetype == 'file') {
                        $files[] = $dir . $ds . $filename;
                    }
                }
            }
            closedir($dir_p);
        }
        return $files;
    }
}
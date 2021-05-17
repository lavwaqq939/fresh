<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件


/**
 * 获取时间
 */
function getTime()
{
    return date('Y-m-d H:i:s');
}

/**
 * 返回项目根目录
 * @return string
 */
function getDomain()
{
    return request()->domain() . request()->root() . "/";
}

/**
 * 中文截取数组
 * @param $cont
 * @param $l
 * @param $utf
 * @return array
 */
function subpart($cont, $l, $utf)
{
    $len = mb_strlen($cont, $utf);
    $arr = [];
    for ($i = 0; $i < $len; $i += $l) {
        $arr[] = mb_substr($cont, $i, $l, $utf);
    }
    return $arr;
}

/**
 * 检测上传目录，目录不存在则创建目录
 * @param string $directory 待检测目录
 * @return boolean 目录存在或已创建返回true，创建失败返回false
 */
function checkDirectory($directory)
{
    $dirStatus = is_dir($directory);
    if (!$dirStatus) {
        //创建目录
        $dirStatus = mkdir($directory, 0777, true);
    }
    return $dirStatus;
}

/**
 * 删除目录及目录下所有文件或删除指定文件
 * @param str $path 待删除目录路径
 * @param int $delDir 是否删除目录，1或true删除目录，0或false则只删除文件保留目录（包含子目录）
 * @return bool 返回删除状态
 */
function delDirAndFile($path, $delDir = FALSE)
{
    $handle = opendir($path);
    if ($handle) {
        while (false !== ($item = readdir($handle))) {
            if ($item != "." && $item != "..")
                is_dir("$path/$item") ? delDirAndFile("$path/$item", $delDir) : unlink("$path/$item");
        }
        closedir($handle);
        if ($delDir)
            return rmdir($path);
    } else {
        if (file_exists($path)) {
            return unlink($path);
        } else {
            return FALSE;
        }
    }
}

/**
 * 生成随机字符串
 * @param $length
 * @param $type 1 全部  2数字  3大写字母  4小写字母
 * @return null|string
 */
function getRandChar($length, $type = 1)
{
    $str = null;
    $strPol = '';
    switch ($type) {
        case 1:
            $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
            break;
        case 2:
            $strPol = "0123456789";
            break;
        case 3:
            $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
            break;
        case 4:
            $strPol = "abcdefghijklmnopqrstuvwxyz";
            break;
        default:
            $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
    }
    $max = strlen($strPol) - 1;

    for ($i = 0; $i < $length; $i++) {
        $str .= $strPol[rand(0, $max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
    }

    return $str;
}

/**
 * 自定义错误日志
 * @param $e
 */
function errorlog($e)
{
    trace("message:" . $e->getMessage(), 'error_log');
    trace("code:" . $e->getCode(), 'error_log');
    trace("file:" . $e->getFile(), 'error_log');
    trace("line:" . $e->getLine(), 'error_log');
}

/**
 * 验证身份证
 * @param string $id cardId
 * @return
 */
function checkIdCard($id)
{
    dump($id);
    $id = strtoupper($id);
    $regx = "/(^\d{15}$)|(^\d{17}([0-9]|X)$)/";
    $arr_split = array();
    if (!preg_match($regx, $id)) {
        return FALSE;
    }
    if (15 == strlen($id)) //检查15位
    {
        $regx = "/^(\d{6})+(\d{2})+(\d{2})+(\d{2})+(\d{3})$/";

        @preg_match($regx, $id, $arr_split);
        //检查生日日期是否正确
        $dtm_birth = "19" . $arr_split[2] . '/' . $arr_split[3] . '/' . $arr_split[4];
        if (!strtotime($dtm_birth)) {
            return FALSE;
        } else {
            return TRUE;
        }
    } else      //检查18位
    {
        $regx = "/^(\d{6})+(\d{4})+(\d{2})+(\d{2})+(\d{3})([0-9]|X)$/";
        @preg_match($regx, $id, $arr_split);
        $dtm_birth = $arr_split[2] . '/' . $arr_split[3] . '/' . $arr_split[4];
        if (!strtotime($dtm_birth)) //检查生日日期是否正确
        {
            return FALSE;
        } else {
            //检验18位身份证的校验码是否正确。
            //校验位按照ISO 7064:1983.MOD 11-2的规定生成，X可以认为是数字10。
            $arr_int = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
            $arr_ch = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
            $sign = 0;
            for ($i = 0; $i < 17; $i++) {
                $b = (int)$id{$i};
                $w = $arr_int[$i];
                $sign += $b * $w;
            }
            $n = $sign % 11;
            $val_num = $arr_ch[$n];
            if ($val_num != substr($id, 17, 1)) {
                return FALSE;
            } else {
                return TRUE;
            }
        }
    }
}

/**
 * 安全URL编码
 * @param type $data
 * @return type
 */
function secureUrlEncode($data)
{
    return str_replace(array('+', '/', '='), array('-', '_', ''), base64_encode(serialize($data)));
}

/**
 * 安全URL解码
 * @param type $string
 * @return type
 */
function secureUrlDecode($string)
{
    $data = str_replace(array('-', '_'), array('+', '/'), $string);
    $mod4 = strlen($data) % 4;
    ($mod4) && $data .= substr('====', $mod4);
    return unserialize(base64_decode($data));
}
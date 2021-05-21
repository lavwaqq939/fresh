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

/**
 * 发送短信
 * @param $tel String 手机号
 * @param $tpl string 短信模板
 * @param $data array 短信数据
 * @return array
 */
function sendSms($tel, $tpl, $data)
{
    //这里的路径EXTEND_PATH就是指tp5根目录下的extend目录，系统自带常量。alisms为我们复制api_sdk过来后更改的目录名称
    require_once EXTEND_PATH.'alisms/vendor/autoload.php';
    \Aliyun\Core\Config::load();             //加载区域结点配置
    $accessKeyId = config("ALI_VCODE.key");  //阿里云短信获取的accessKeyId
    $accessKeySecret =  config("ALI_VCODE.secret"); //阿里云短信获取的accessKeySecret
    //这个个是审核过的模板内容中的变量赋值，记住数组中字符串code要和模板内容中的保持一致
    //比如我们模板中的内容为：你的验证码为：${code}，该验证码5分钟内有效，请勿泄漏！
    $templateParam = $data;           //模板变量替换
    $signName = config("ALI_VCODE.sign_name"); //这个是短信签名，要审核通过
    $templateCode = $tpl;   //短信模板ID，记得要审核通过的
    //短信API产品名（短信产品名固定，无需修改）
    $product = "Dysmsapi";
    //短信API产品域名（接口地址固定，无需修改）
    $domain = "dysmsapi.aliyuncs.com";
    //暂时不支持多Region（目前仅支持cn-hangzhou请勿修改）
    $region = "cn-hangzhou";
    // 初始化用户Profile实例
    $profile = \Aliyun\Core\Profile\DefaultProfile::getProfile($region, $accessKeyId, $accessKeySecret);
    // 增加服务结点
    \Aliyun\Core\Profile\DefaultProfile::addEndpoint("cn-hangzhou", "cn-hangzhou", $product, $domain);
    // 初始化AcsClient用于发起请求
    $acsClient= new \Aliyun\Core\DefaultAcsClient($profile);
    // 初始化SendSmsRequest实例用于设置发送短信的参数
    $request = new \Aliyun\Api\Sms\Request\V20170525\SendSmsRequest();
    // 必填，设置雉短信接收号码
    $request->setPhoneNumbers($tel);
    // 必填，设置签名名称
    $request->setSignName($signName);
    // 必填，设置模板CODE
    $request->setTemplateCode($templateCode);
    // 可选，设置模板参数
    if($templateParam) {
        $request->setTemplateParam(json_encode($templateParam));
    }
    //发起访问请求
    $acsResponse = $acsClient->getAcsResponse($request);
    //返回请求结果
    $result = json_decode(json_encode($acsResponse),true);
    if ($result["Code"] == "OK") {
        return array(
            "result" => true,
            "message" => ""
        );
    } else {
        return array(
            "result" => false,
            "message" => $result["Message"]
        );
    }
}

/**
 * 验证手机号
 */
function checkTelFormat($phoneNumber)
{
    $pattern = "/^1[3456789]\d{9}$/";
    $phoneStatus = preg_match($pattern, $phoneNumber);
    if ($phoneStatus == 1) {
        return true;
    } else {
        return false;
    }
}
/**
 * 返回小程序信息
 */
function getxcxInfo($code,$options){
    $appid= $options["app_id"];
    $app_secret = $options["secret"];
    $app = "https://api.weixin.qq.com/sns/jscode2session?appid=$appid&secret=$app_secret&js_code=$code&grant_type=authorization_code";
    $userService = file_get_contents($app);
    $userService = json_decode($userService,true);
    return $userService;
}

/**
 * 微信支付
 * @param $info 订单信息一维数组
 * @param $name 商品名称 字符串
 * @param $attach 类型  1 支付订单 2充值
 * @param $type 区分支付端
 * @return array
 */
function weChatPay($info,$name,$attach,$wx_openid=null){
    $payResult = [];
    $trade_type = $wx_openid?'JSAPI':'APP';
    //$attach 为1支付2充值3积分商品支付运费
    $attributes = [
        'trade_type'       => $trade_type, // JSAPI，NATIVE，APP...
        'body'             => $name,
        'detail'           => $name,
        'out_trade_no'     => $info["order_no"],
        'total_fee'        => $info["order_money"], // 单位：分
        "attach"           => $attach,
    ];
    $easyWeChat = easyWeChat();
    $payment = $easyWeChat->payment;
    $order = new \EasyWeChat\Payment\Order($attributes);
    $result = $payment->prepare($order);
    //var_dump($result);die;
    if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS'){
        $prepayId = $result->prepay_id;
        $payResult["result"]  = true;
        if($wx_openid){
            $payResult["msg"] = $payment->configForJSSDKPayment($prepayId);
        }else{
            $payResult["msg"] = $payment->configForAppPayment($prepayId);
        }

    } else {
        $payResult["result"]  = false;
        $payResult["msg"] = "下单失败";
    }
    return $payResult;
}
//实例化
function easyWeChat($option=[]){
    $options = $option ?$option:config("xcx_config");
    $app = new \EasyWeChat\Foundation\Application($options);
    return $app;
}
/**
 * /*
 * 微信退款
 * @param $type int 微信配置 1开放 !1 公众
 * @param $orderNo string 订单号
 * @param $refundNo string 退款单号
 * @param $total price 总金额 单位分
 * @param $refundFee price 退款金额 单位分
 * @return array
 */
function weChatTradeRefund($type,$orderNo,$refundNo,$total,$refundFee){
    if($type == 1){
        $options = config("open_Platform");
    }else {
        $options = config("xcx_config");
    }
    $app = easyWeChat($options);
    $payment = $app->payment;
    $result = $payment->refund($orderNo, $refundNo, $total, $refundFee);
    $res = json_decode($result,TRUE);
//    return $res;
    if($res["return_code"]!=="SUCCESS"){
        $msg = [
            "msg"=>$res["return_msg"],
            "result"=>false
        ];
    }else{
        $msg = [
            "msg"=>'SUCCESS',
            'result'=>true
        ];
    }
    return $msg;
}
//返回订单编号
function getOrderNo(){
    $rand = rand(1000,9999);
    return date("YmdHis").$rand;
}
//返回随机字符串
function sessionStr(){
    return rand(1000, 9999) . date('YmdHis') . 'dyxsxcx';
}
//小程序解密
function xcxDecrypt($appid, $sessionKey,$encryptedData, $iv)
{
    require_once ROOT_PATH.'/extend/wxxcx/wxBizDataCrypt.php';
    $pc = new WXBizDataCrypt($appid, $sessionKey);
    $errCode = $pc->decryptData($encryptedData, $iv, $data );
    if ($errCode == 0) {
        //$data = json_decode($data,1);
        // print($data . "\n");
        return $data;
    } else {
        // print($errCode . "\n");
        return $errCode;
    }
}
//小程序推送
function xcxPush($openid,$template_id,$form_id,$data)
{
    $xcx = config("xcx_config");
    $arr = [
        "grant_type"=>"client_credential",
        "appid"=>$xcx['app_id'],
        "secret"=>$xcx['secret']
    ];
    $r = http_request("https://api.weixin.qq.com/cgi-bin/token",$arr);
    $access_token = json_decode($r,true);
    $data = [
        "touser"=>$openid,//用户openid
        "template_id"=>$template_id,//模板id
        "form_id"=>$form_id,//form_id
        "data"=>$data,//二维数组
//        "data"=>[
//            "keyword1"=>[
//                "value"=>"收到没",
//                "color"=>"#cccccc"
//            ],
//            "keyword2"=>[
//                "value"=>"收到了",
//                "color"=>"#cccccc"
//            ]
//        ]
    ];
    $ast = $access_token['access_token']; //ACCESS_TOKEN
    $rdata = http_request("https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=".$ast, $data,"json");
    $rdata = json_decode($rdata,true);
    if($rdata['errcode']==0){
        return true;
    } else {
        return false;
    }
}
//curl小程序推送用
function http_request($url,$data,$type="http")
{
    $curl = curl_init();
    if ($type == "json"){
        $headers = array("Content-type: application/json;charset=UTF-8");
        $data=json_encode($data);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    }
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    if (!empty($data)){
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS,$data);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}
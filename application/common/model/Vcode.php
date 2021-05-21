<?php
/**
 * Created by PhpStorm.
 * User: smk19
 * Date: 2018/4/3
 * Time: 9:13
 */

namespace app\common\model;


use think\Model;

class Vcode extends Model
{
    /**
     * 发送验证码
     * $telephone
     * @param string $telephone 手机号
     * @return boolean
     */
    public function sendCode($telephone){
        if(!checkTelFormat($telephone)) return false;
        $vcode = rand(100000,999999);
//        $vcode = 123456;
        $sendResult = sendSms($telephone,config("ALI_VCODE.ALIDAYU_VCODE_TPL"),["code"=>$vcode]);
        $time = time();
        if(!$sendResult["result"]) return false;
        $where = [
            "telephone"=>$telephone,
            "code"=>$vcode,
            "create_time"=>$time,
            "invalid_time"=>$time+(config('ALI_VCODE.vcode_time')*60)
        ];
        $insert = $this->insert($where);
        if(!$insert) return false;
        return true;
    }

    /**
     * @param $telephone 手机号
     * @param $code 验证码
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function checkCode($telephone,$code){
        $result["result"] = true;
        $where = [
            "telephone"=>$telephone,
            "code"=>$code,
            "invalid_time"=>["egt",time()],
            "valid"=>1
        ];
        $find = $this->where($where)->order("vcode_id desc")->find();
        if(!$find) {
            $result["result"] = false;
            $result["msg"] = "验证码已失效,请重新发送";
            return $result;
        }
        $this->save(["valid"=>2],["vcode_id"=>$find["vcode_id"]]);
        return $result;
    }

}
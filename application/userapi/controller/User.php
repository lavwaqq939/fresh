<?php
namespace app\userapi\controller;
use think\Db;
use think\Exception;

class User extends Base
{
    //小程序登录
    public function xcxLogin()
    {
        $user_id = input('user_id');
        if(isset($user_id)) {
            $data = model('User')->getUserInfo($user_id);
            if($data)$this->_ajax_return(200,'操作成功',$data);
            else $this->_ajax_return(603,'网络错误，请稍后再试！');
        } else {
            $code = input('wxCode');
            $telephone = input('telephone');
            $nickname = input('nickname');
            $headimg = input('headimg');
            if(!$code ||!$telephone ||!$nickname ||!$headimg)  $this->_ajax_return(603,'缺少参数！');
            if(!checkTelFormat($telephone)) $this->_ajax_return(603,'手机号格式错误！');
            $count = db('user')->where(['telephone'=>$telephone,'status'=>1])->count();
            if($count) $this->_ajax_return(603,'手机号已存在！');
            $options = config("xcx_config");
            $res = getxcxInfo($code,$options);
            if(!isset($res['session_key'])) {
                $this->_ajax_return(603,'网络错误，请稍后再试！');
            }
            $arr = [
                'openid' => $res['openid'],
                'nickname' => $nickname,
                'headimg' => $headimg,
                'create_time'=>date("Y-m-d H:i:s"),
                'telephone'=>$telephone,
            ];
            db()->startTrans();
            try{
                $id = db('user')->insertGetId($arr);
                db('user_info')->insert(['user_id'=>$id]);
                $data = model('User')->getUserInfo($id);
                db()->commit();
                $this->_ajax_return(200,'操作成功',$data);
            }catch (\Exception $e){
                db()->rollback();
                $this->_ajax_return(603,'网络错误，请稍后再试！');
            }
        }
    }
    //轮播图
    public function bannerList()
    {
        $where = [
            'banner_status'=>1,
        ];
        $banner = model('Banner')->getBanner($where);
        $this->_ajax_return(200,'操作成功',$banner);
    }
    //门店列表 门店评分计算没解决？？
    public function storeList()
    {
        $type = input('type');//type为1距离最近为2评分最高
        $longitude = input('longitude');//经度
        $latitude = input('latitude');//纬度
//        http://restapi.amap.com/v3/distance?origins=$radLat1&destination=$weizhi&output=JSON&type=0&key=409b460dc7afa24c9a18db0ee0e0dfdc

    }
    //我的收获地址列表
    public function addressList()
    {
        $user_id = input('user_id');
        $where = [
            'user_id'=>$user_id,
            'address_status'=>1,
        ];
        $data = db('user_address')->where($where)->select();
        $this->_ajax_return(200,'操作成功',$data);
    }
    //新增收货地址
    public function addAddress()
    {
        $data = input('post.');
        $Validate = new \app\common\validate\User();
        $msg = $Validate->scene("checkaddAddress")->check($data);
        if(!$msg) $this->_ajax_return(603,$Validate->getError());
        if(!checkTelFormat($data['telephone'])) $this->_ajax_return(603,'手机号格式错误！');
        $res = db('user_address')->insert($data);
        if($res) $this->_ajax_return(200,'操作成功');
        else $this->_ajax_return(603,'操作失败');
    }
    //收货地址详细信息
    public function addressInfo()
    {
        $id = input('user_address_id');
        $data = db('user_address')->where('user_address_id',$id)->find();
        $this->_ajax_return(200,'操作成功',$data);
    }
    //修改收货地址
    public function updAddress()
    {
        $data = input('post.');
        $Validate = new \app\common\validate\User();
        $msg = $Validate->scene("checkaddAddress")->check($data);
        if(!$msg) $this->_ajax_return(603,$Validate->getError());
        if(!$data['user_address_id']) $this->_ajax_return(603,'缺少参数！');
        if(!checkTelFormat($data['telephone'])) $this->_ajax_return(603,'手机号格式错误！');
        $where = [
            'user_address_id'=>$data['user_address_id']
        ];
        $res = db('user_address')->where($where)->update($data);
        if($res) $this->_ajax_return(200,'操作成功');
        else $this->_ajax_return(200,'操作失败');
    }
    //删除收货地址
    public function delAddress()
    {
        $user_address_id = input('user_address_id');
        $address_status = db('user_address')->where('user_address_id',$user_address_id)->value('address_status');
        if($address_status==3) $this->_ajax_return(603,'系统繁忙，请稍后再试');
        $res = db('user_address')->where('user_address_id',$user_address_id)->update(['address_status'=>2]);
        if($res) $this->_ajax_return(200,'操作成功');
        else $this->_ajax_return(603,'操作失败');
    }
    //判断支付密码是否正确
    public function checkPay()
    {
        $data = input("post.");
        $userValidate = new \app\common\validate\User();
        $msg = $userValidate->scene("pay")->check($data);
        if(!$msg) $this->_ajax_return(603,$userValidate->getError());
        else $this->_ajax_return(200,"验证正确");
    }
    //设置支付密码
    public function addPayPassword(){
        $data = input("post.");
        $this->privatePay($data,"addPayPassword");
    }
    //修改支付密码
    public function revisePayPassword(){
        $data = input("post.");
        $this->privatePay($data,"revisePayPassword");
    }
    //忘记支付密码
    public function forgetPayPassword(){
        $data = input("post.");
        $this->privatePay($data,"forgetPayPassword");
    }
    //添加 修改 忘记支付密码
    private function privatePay($data,$type){
        $userValidate = new \app\common\validate\User();
        $msg = $userValidate->scene($type)->check($data);
        if(!$msg) $this->_ajax_return(603,$userValidate->getError());
        $where = [
            "pay_password"=>$data["pay_password"],
            "user_id"=>$data['user_id']
        ];
        $update = db("user")->update($where);
        if($update === false) $this->_ajax_return(603,"操作失败");
        $this->_ajax_return(200,"操作成功");
    }
    //收藏门店 取消收藏
    public function collectStore()
    {
        $user_id  = input('user_id');
        $store_id = input('store_id');
        $type = input('type');//type1收藏2取消
        if(!$user_id || !$store_id ||!$type) $this->_ajax_return(603,"缺少参数");
        $arr = [
            'user_id'=>$user_id,
            'store_id'=>$store_id,
        ];
        if($type==1){
            $user_collect = db('user_collect')->where($arr)->find();
            if($user_collect) $this->_ajax_return(603,'您已经收藏过该门店');
            $res = db('user_collect')->insert($arr);
        }else{
            $res = db('user_collect')->where($arr)->delete();
        }
        if($res) $this->_ajax_return(200,'操作成功');
        else $this->_ajax_return(603,'操作失败');
    }
    //个人信息
    public function userInfo()
    {
        $user_id  = input('user_id');
        $data = model('User')->getUserInfo($user_id);
        $this->_ajax_return(200,'操作成功',$data);
    }

}
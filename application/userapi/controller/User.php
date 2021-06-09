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
    //门店列表
    public function storeList()
    {
        $page = input('page');
        $type = input('type');//type为1距离最近为2评分最高
        $user_id = input('user_id');
        $longitude = input('longitude');//经度
        $latitude = input('latitude');//纬度
        $goods_name = input('goods_name');
        if(!$page || !$type ||!$user_id ||!$longitude ||!$latitude){
           $this->_ajax_return(603,'缺少参数');
        }
        if(isset($goods_name)) {
            $g_where = [
                's.status' => 1,
                'g.goods_name' => ["like", "%" . $goods_name . "%"],
            ];
            $store_id = db('goods')->alias('g')
                ->join('store_goods s', 's.goods_id = g.goods_id')
                ->where($g_where)
                ->column('store_id');
            $data = model('Store')->queryRange($longitude,$latitude,$page,$user_id,$store_id);
        } else {
            $data = model('Store')->queryRange($longitude,$latitude,$page,$user_id);
        }
        if($type==1) {
            $edition = [];
            foreach ($data as $k => $v) {
                $edition[] = $v['distance'];
            }
            if(is_array($edition)&& !empty($edition)){
                array_multisort($edition, SORT_ASC, $data);
            }
        }else {
            $edition = [];
            foreach ($data as $k => $v) {
                $edition[] = $v['score'];
            }
            if(is_array($edition)&& !empty($edition)){
                array_multisort($edition, SORT_DESC, $data);
            }
        }
        $this->_ajax_return(200,'操作成功',$data);
    }
    //门店分类列表
    public function cateList()
    {
        $store_id = input('store_id');
        $where = [
            's.status' => 1,
            's.store_id' => $store_id,
            'c.status' =>1,
        ];
        $field ="g.category_id,c.category_name";
        $cate = model('Store')->getStoreCate($field,$where);
        $this->_ajax_return(200,'操作成功',$cate);
    }
    //门店商品列表
    public function goodsList()
    {
        $store_id = input('store_id');
        $category_id = input('category_id');
        $page = input('page');
        if(!$store_id || !$category_id ||!$page) $this->_ajax_return(603,'缺少参数');
        $where = [
            's.status' => 1,
            's.store_id' => $store_id,
            'c.category_id' =>$category_id,
        ];
        $field ="g.goods_id,g.goods_name,g.goods_img,g.goods_spe,g.goods_price,s.evaluate_count,s.five_star_count";
        $goods = model('Store')->getStoreGoods($field,$where,$page);
        foreach($goods as $k=>$v) {
            $goods[$k]['praiserate'] = $goods[$k]['five_star_count'] / $goods[$k]['evaluate_count'];
            $goods[$k]['praiserate'] = (number_format($goods[$k]['praiserate'], 2)*100).'%';
        }
        $this->_ajax_return(200,'操作成功',$goods);
    }
    //门店信息
    public function storeInfo()
    {
        $store_id = input('store_id');
        $field = "store_id,store_name,store_img,store_address,store_telephone,business_start,business_end,dispatch_start,dispatch_end,score";
        $where = [
            'store_id'=>$store_id,
            'status'=>1,
        ];
        $store = model("Store")->getStoreInfo($field,$where);
        $this->_ajax_return(200,'操作成功',$store);
    }
    //商品详细信息
    public function goodsInfo()
    {
        $goods_id = input('goods_id');
        $store_id = input('store_id');
        if(!$store_id || !$goods_id) $this->_ajax_return(603,'缺少参数');
        $where = [
            's.status' => 1,
            's.store_id' => $store_id,
            'g.goods_id' =>$goods_id,
        ];
        $field ="g.goods_id,g.goods_name,g.goods_img,g.goods_spe,g.goods_price,g.goods_discript,s.evaluate_count,s.five_star_count,c.category_name";
        $goods = model('Goods')->getGoodsInfo($field,$where);
        if($goods){
            $goods['praiserate'] = $goods['five_star_count'] / $goods['evaluate_count'];
            $goods['praiserate'] = (number_format($goods['praiserate'], 2)*100).'%';
        }
        $this->_ajax_return(200,'操作成功',$goods);
    }
    //商品评论列表
    public function goodsCommentList()
    {
        $store_id = input('store_id');
        $goods_id = input('goods_id');
        $page = input('page');
        if(!$store_id || !$goods_id || !$page) $this->_ajax_return(603,'缺少参数');
        $where = [
            'c.goods_id'=>$goods_id,
            'c.store_id'=>$store_id,
            'c.status'=>1,
        ];
        $field = "u.user_id,u.nickname,u.headimg,c.stars,c.create_time,c.body,c.comment_id";
        $comment = model('Comment')->getCommentList($field,$where,$page);
        foreach ($comment as $k=>$v) {
            $comment[$k]['img']  = db('comment_info')->field('path')->where('comment_id',$comment[$k]['comment_id'])->select();
        }
        $this->_ajax_return(200,'操作成功',$comment);
    }
    //我的收获地址列表
    public function addressList()
    {
        $user_id = input('user_id');
        $where = [
            'user_id'=>$user_id,
            'address_status'=>1,
        ];
        $data = db('user_address')->where($where)->order('user_address_id desc')->select();
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
    //结算信息
    public function clearingInfo(){
        $user_id = input('user_id');
        $goods_id = input('goods_id');
        $store_id = input('store_id');
        $goods_number = input('goods_number');
        if(!$goods_id || !$store_id ||!$goods_number ||!$user_id) {
            $this->_ajax_return(603,"缺少参数");
        }
        $goods_id = explode(',',$goods_id);
        $goods_number = (explode(',',$goods_number));
        $where = [
            'sg.store_id'=>$store_id,
            'sg.goods_id'=>['in',$goods_id],
            'sg.status'=>1,
        ];
        $field = "s.store_name,g.goods_id,g.goods_name,g.goods_img,g.goods_price";
        $data['goods'] = model('Goods')->getGoodsSelect($field,$where);
        $total = 0.00;
        $goods_num = 0;
        foreach ($data['goods'] as $k=>$v){
            $number = $goods_number[$k];
            $data['goods'][$k]['price'] = $data['goods'][$k]['goods_price'] * $number;
            $data['goods'][$k]['number'] = $number;
            $total += ($data['goods'][$k]['goods_price'] * $number);
            $goods_num += $number;
        }
        $c_where = [
            'ucr.user_id'=>$user_id,
            'ucr.is_use'=>0,
            'c.end_time'=>['>',date("Y-m-d H:i:s")],
            'c.full_money'=>['<=',$total],
        ];
        $data['coupon'] = model('Coupon')->getDoCoupon($c_where);
        foreach ($data['coupon'] as $key=>$val){
            $data['coupon'][$key]['end_time'] = str_replace("-",".",substr($data['coupon'][$key]['end_time'],0,10));
        }
        $data['goods_num'] = $goods_num;
        $data['total'] = $total;
        $this->_ajax_return(200,"操作成功",$data);
    }
    //获取配送费
    public function getFreight()
    {
        $store_id = input('store_id');
        $address_id = input('user_address_id');
        if(!$store_id ||!$address_id) $this->_ajax_return(603,"缺少参数");
        $y_where = [
            'type'=>['in',[1,2]],
            'status'=>1,
        ];
        $freight = db('config')->where($y_where)->find();
        if($freight['type']==1){
           $store = db('store')->where('store_id',$store_id)->find();
           $user_address = db('user_address')->where('user_address_id',$address_id)->find();
           //计算距离
           $destination = $store['longitude'].','.$store['latitude'];
           $origins = $user_address['longitude'].','.$user_address['latitude'];
           $app = "http://restapi.amap.com/v3/distance?origins=$origins&destination=$destination&output=JSON&type=0&key=d76a846aa32a0dfd9a58e788b16bdc74";
           $Service = file_get_contents($app);
           $Service = json_decode($Service, true);
           if($Service['status']!=1) $this->_ajax_return(603,"网络错误！");
           foreach ($Service['results'] as $key=>$val){
                $distance = $val['distance'];
           }
           //向上取整获取运费
           $data['freight'] = $freight['value'] * (ceil($distance/1000));
        } else {
           $data['freight'] = $freight['value'];
        }
        $this->_ajax_return(200,"操作成功",$data);
    }
    //协议编辑1 关于我们2  会员权益5
    public function information()
    {
        $id = input('system_id');
        $data['info'] = db('system')->where('system_id',$id)->value('value');
        $this->_ajax_return(200,"操作成功",$data);
    }
    //客服信息
    public function customerService()
    {
        $data['info'] = db('system')->where('system_id',3)->value('value');
        $data['phone'] = db('system')->where('system_id',4)->value('value');
        $this->_ajax_return(200,"操作成功",$data);
    }
    //修改手机号
    public function updPhone()
    {
        $user_id = input('user_id');
        $telephone = input('newTelephone');
        $code = input('code');
        if(!$telephone ||!$code ||!$user_id) $this->_ajax_return(603,"缺少参数");
        if(!checkTelFormat($telephone)) $this->_ajax_return(603,"手机号格式错误");
        $where = [
            'telephone'=>$telephone,
            'status'=>1,
        ];
        $count = db('user')->where($where)->count();
        if ($count) $this->_ajax_return(603,"手机号已存在!");
        $result = model('Vcode')->checkCode($telephone,$code);
        if(!$result["result"]) $this->_ajax_return(603,$result["msg"]);
        $res = db('user')->where('user_id',$user_id)->update(['telephone'=>$telephone]);
        if($res) $this->_ajax_return(200,'操作成功');
        else $this->_ajax_return(603,'系统繁忙，请稍后再试');
    }
    //收藏门店列表
    public function collectionList()
    {
        $page = input('page');
        $user_id = input('user_id');
        $longitude = input('longitude');//经度
        $latitude = input('latitude');//纬度
        if(!$page || !$user_id ||!$longitude||!$latitude) $this->_ajax_return(603,'缺少参数');
        $store_id = db('user_collect')->where('user_id',$user_id)->column('store_id');
        $data = model('Store')->queryRange($longitude,$latitude,$page,$user_id,$store_id);
        $this->_ajax_return(200,'操作成功',$data);
    }
    //反馈
    public function feedback()
    {
        $user_id = input('user_id');
        $content = input('content');
        if(!$user_id ||!$content) $this->_ajax_return(603,'缺少参数');
        $arr = [
            'user_id'=>$user_id,
            'content'=>$content,
        ];
        $res = db('feedback')->insert($arr);
        if($res) $this->_ajax_return(200,'操作成功');
        else $this->_ajax_return(603,'系统繁忙，请稍后再试');
    }
    //优惠券列表
    public function couponList()
    {
        $user_id = input('user_id');
        $where = [
            'ucr.user_id'=>$user_id,
        ];
        $data = model('Coupon')->getDoCoupon($where);
        foreach($data as $k=>$v){
            $data[$k]['end_time'] = str_replace("-",".",substr($data[$k]['end_time'],0,10));
            if($data[$k]['is_use'] == 0 && $data[$k]['end_time'] < date("Y-m-d H:i:s")){
                $data[$k]['is_use']=3;
            }
        }
        $this->_ajax_return(200,'操作成功',$data);
    }
    //钱包及记录
    public function myWallet()
    {
        $user_id = input('user_id');
        $page = input('page');
        if(!$page || !$user_id) $this->_ajax_return(603,'缺少参数');
        $data['money'] = db('user_info')->where('user_id',$user_id)->value('account');
        $field = "total_fee,type,create_time";
        $data['moneyLog'] = db('account_log')->field($field)->where('user_id',$user_id)->order('account_log_id desc')->page($page,config("page"))->select();
        $this->_ajax_return(200,"操作成功",$data);
    }
}
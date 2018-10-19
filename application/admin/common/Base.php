<?php
namespace app\admin\common;
use think\Controller;
use think\Session;
class Base extends Controller
{
     protected function _initialize()
    {
        parent::_initialize();
        //在公共控制器的初始化方法中,创建一个常量用来判断用户是否登录或已登录
        define('USER_ID', Session::get('user_id'));
		$request = \think\Request::instance();
		$action =  $request->action();
		$no_login_action = array('login','check','logout');
        if(!in_array(strtolower($action),$no_login_action)&&(is_null(USER_ID))){
                $this -> error('未登录,无权访问~~', 'login/login');
        }
    }
	//定义空方法
	public function _empty(){
        $this->error('页面不存在','login/login');

    }

    /**
     * @return array
     */
    public function certify(){
        $username = Session::get('user_id');
        $role = implode('',db('user')->where('username',$username)->column('role'));
        $authority = db('role')->where('role',$role)->column('authority');
        $authority = implode('',$authority);
        $authorityArray = explode(',',$authority);
        $rule = db('rule')->whereIn('name',$authorityArray)->column('rule');
        return $rule;
    }

    public function  weather(){
        $curlobj = curl_init();
        curl_setopt($curlobj,CURLOPT_URL,"http://wthrcdn.etouch.cn/weather_mini?city=合肥");
        curl_setopt($curlobj,CURLOPT_HEADER,0);
        curl_setopt($curlobj,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($curlobj, CURLOPT_ENCODING, "gzip");
        curl_setopt($curlobj, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.152 Safari/537.36');
        $rtn = curl_exec($curlobj);
        if(!curl_errno($curlobj)){
            $rtn = json_decode($rtn,0);
            return  $rtn;
        }else{
            echo 'Curl error: '.curl_errno($curlobj);
        }
        curl_close($curlobj);
    }
}

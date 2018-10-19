<?php

namespace app\admin\controller;
use app\admin\common\Base;
use think\Request;
//use app\admin\model\Admin;
use think\Session;
//use think\Cookie;
class Login extends Base
{
    /**
     * 渲染登陆页面
     *
     * @return \think\Response
     */
    public function login()
    {
        return $this->fetch('login');
    }

    /**
     * 验证用户身份
     *
     * @param Request $request
     * @return void
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function check(Request $request)
    {
        $weather = parent::weather();
        $date = date('Y-m-d');
        if(!is_null($weather)){
            $array = $weather->data->forecast;
            //var_dump($array);die;
            $this->assign('date',$date);
            $this->assign('high',$array[0]->high);
            $this->assign('low',$array[0]->low);
            Session::set('date',$date);
            Session::set('high',$array[0]->high);
            Session::set('low',$array[0]->low);
        }
        //登陆验证
		$data = $request->param();
		$username = $data['username'];
		$password = md5($data['password']);
       // $map = ['username'=>$username];
	   // $admin = Admin::get($map); 模块操作数据库表的写法
		$user = db('user')->where('username',$username)->find();
		if(is_null($user)){
			
			$this->error('用户名不正确');
			
		} elseif($user['password']!=$password){
			
			$this->error('密码不正确');
					
		} else {
			Session::set('user_id',$username);
			Session::set('user_info',$data);
           //使用setInc('login_count',number)也可以实现某个整形字段的递增；
            $res = db('user')
                ->where('username', $data['username'])
                ->update([
                    'login_count'=>['exp','login_count+1'],
                    'last_time' => date('Y/m/d')
                ]);
            if(!is_null($res)){
                $this->success('登陆成功','index/index',3);
            }
		}
    }
    /**
     * 退出登陆
     *
     * @return void
     */
    public function logout()
    {
          //删除当前用户session值
        //Session::delete('user_id');
        //Session::delete('user_info');
		session('user_info',null);
        session('user_id',null);
        //执行成功,并返回登录界面
        $this -> success('注销成功,正在返回...','login');
    }
	
}
<?php
namespace app\admin\controller;
use app\admin\common\Base;
use think\Session;

class Index extends Base
{
    public function index()
    {
		return $this->fetch('index');
    }
	
	public function welcome()
	{
		
		return $this->fetch('welcome');
	}
	
    public function modify()
    {
        return $this->fetch('modify');
    }

    /**
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function save()
    {

        if($this->request->isPost()){
			$data = $this->request->param(true);//true包含文件
		} else {
			$this->error('请求类型错误~~');	
		}
	    $username = Session::get('user_id');
        if (!empty($data)) {
            $password = md5($data['password']);
        }
        if (!empty($password)) {
            $res = db('user')->where('username',$username)->update(['password'=>$password]);
        }
        if (!empty($res)) {
            if($res=='1'){
                $this->success('修改成功!');
            } else if($res=='0'){
                $this->error('新密码与原密码一致，无需修改');
            } else {
                $this->error('修改失败,请联系管理员!');
            }
        }
    }
}

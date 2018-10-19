<?php

namespace app\admin\controller;
use app\admin\common\Base;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\Exception;
use think\exception\DbException;
use think\exception\PDOException;
use think\Request;

class admin extends Base
{
    /**
     * 显示会员列表
     *
     * @return \think\Response
     * @throws \think\exception\DbException
     */
    public function index()
    {
		$user = db('user')->order('id','asc')
		     ->paginate(10,false,[
		     'type' => 'bootstrap',
		     'var_page' => 'page',
		]);
		$count = db('user')->count();
      	$this->assign('user',$user);
		$this->assign('count',$count);
        return $this->fetch('admin_list');
    }
    /**
     * @return mixed
     * @throws DbException
     */
    public function role()
    {
        $role = db('role')->order('id','asc')
            ->paginate(10,false,[
                'type' => 'bootstrap',
                'var_page' => 'page',
            ]);
        $count = db('role')->count();
        $this->assign('role',$role);
        $this->assign('count',$count);
        return $this->fetch('admin_role');
    }
	
	public function roleAdd()
    {
        return $this->fetch('role_add');
		
    }

    public function ruleAdd()
    {
        return $this->fetch('rule_add');

    }

    public function roleSave(Request $request)
    {
        if($request->isPost()){
            $data = $request->param();
            $data['authority'] = implode(",",$data['authority']);
            $res = db('role')
                ->data([
                    'role'=>$data['role'],
                    'authority'=>$data['authority'],
                    'status'=>0,
                    'remark'=>$data['remark'],
                ])
                ->insert($data);
            if(is_null($res)){
                $this->error('新增失败','index',3);
            }
            $this->success('新增成功','index',3);

        } else {
            $this->error('请求类型错误!','index',3);
        }
    }

    public function ruleSave(Request $request)
    {
        if($request->isPost()){
            $data = $request->param();
            $res = db('rule')
                ->data([
                    'rule'=>$data['rule'],
                    'name'=>$data['name'],
                    'sort'=>$data['sort']
                ])
                ->insert($data);
            if(is_null($res)){
                $this->error('新增失败','index',3);
            }
            $this->success('新增成功','index',3);

        } else {
            $this->error('请求类型错误!','index',3);
        }
    }

	public function cate()
    {
        return $this->fetch('admin_cate');
				
    }

    /**
     * @return mixed
     * @throws DbException
     */
    public function rule()
    {
        $rule = db('rule')->order('id','asc')
            ->paginate(10,false,[
                'type' => 'bootstrap',
                'var_page' => 'page',
            ]);
        $count = db('rule')->count();
        $this->assign('rule',$rule);
        $this->assign('count',$count);
        return $this->fetch('admin_rule');
    }
	
    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        return $this->fetch('admin_add');
    }

    /**
     * @return mixed
     * @throws DbException
     */
    public function search()
    {
        $username = input('username');
        $where['username'] = array('like','%'.$username.'%');
        //paginate的第三个参数：
        $user = db('user')->where($where)->paginate(10,false,['query'=> request()->param()]);
        $count = db('user')->where($where)->count();
        $this->view->assign('user',$user);
        $this->view->assign('count',$count);
        return $this->fetch('admin_list');
    }
    /**
     * 保存新建的资源
     *
     * @param  \think\Request $request
     * @return void
     */
    public function save(Request $request)
    {
        if($request->isPost()){
			 //true包含上传文件
			$data = $request->param(true);
		    $data['password'] = md5($data['password']);
			$data['role'] = implode(",",$data['role']);
			$res = db('user')
			     ->data([
				    'username'=>$data['username'],
		            'firstname'=>$data['firstname'],
		            'password'=>$data['password'],
		            'role'=>$data['role'],
		            'telno'=>$data['telno'],
		            'email'=>$data['email'],
					'create_time'=>date('Y/m/d'),
                     'login_count'=>0
				 ])
			     ->insert($data);
			if(is_null($res)){
			$this->error('新增失败','index',3);
		}
			$this->success('新增成功','index',3);
			
		} else {
			$this->error('请求类型错误!','index',3);
		}
    }
    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        try {
            $data = db('user')->where('id', $id)->find();
        } catch (DataNotFoundException $e) {
        } catch (ModelNotFoundException $e) {
        } catch (DbException $e) {
        }
        if (!empty($data)) {
            $this->view->assign('data',$data);
        }
        return $this->fetch('admin_edit');
    }

    /**
     * @param $id
     * @return mixed
     */
    public function roleEdit($id)
    {
        try {
            $data = db('role')->where('id', $id)->find();
        } catch (DataNotFoundException $e) {
        } catch (ModelNotFoundException $e) {
        } catch (DbException $e) {
        }
        if (!empty($data)) {
            $this->view->assign('data',$data);
        }
        return $this->fetch('role_edit');
    }

    public function ruleEdit($id)
    {
        try {
            $data = db('rule')->where('id', $id)->find();
        } catch (DataNotFoundException $e) {
        } catch (ModelNotFoundException $e) {
        } catch (DbException $e) {
        }
        if (!empty($data)) {
            $this->view->assign('data',$data);
        }
        return $this->fetch('rule_edit');
    }
    /**
     * 保存更新的资源
     *
     * @return void
     */
    public function update()
    {
        $data = $this->request->param(true);
        $data['role'] = implode(",",$data['role']);
        try {
            $res = db('user')
                ->where('id', $data['id'])
                ->update([
                    'username' => $data['username'],
                    'firstname' => $data['firstname'],
                    'password' => md5($data['password']),
                    'role' => $data['role'],
                    'telno' => $data['telno'],
                    'email' => $data['email']
                ]);
        } catch (PDOException $e) {
        } catch (Exception $e) {
        }
        if(!empty($res)){
			$this->error('更新成功','index',3);

		}
			$this->success('更新失败','index',3);
    }

    /**
     */
    public function roleUpdate()
    {
        $data = $this->request->param(true);
        $data['authority'] = implode(",",$data['authority']);
        try {
            $res = db('role')
                ->where('id', $data['id'])
                ->update([
                    'role'=>$data['role'],
                    'authority'=>$data['authority'],
                    'status'=>0,
                    'remark'=>$data['remark']
                ]);
        } catch (PDOException $e) {
        } catch (Exception $e) {
        }
        if(!empty($res)){
            $this->error('更新成功','index',3);
        }
        $this->success('更新失败','index',3);
    }

    public function ruleUpdate()
    {
        $data = $this->request->param(true);
        try {
            $res = db('rule')
                ->where('id', $data['id'])
                ->update([
                    'rule'=>$data['rule'],
                    'name'=>$data['name'],
                    'sort'=>$data['sort']
                ]);
        } catch (PDOException $e) {
        } catch (Exception $e) {
        }
        if(!empty($res)){
            $this->error('更新成功','index',3);
        }
        $this->success('更新失败','index',3);
    }
    /**
     * 删除指定资源
     *
     * @param  int $id
     * @return void
     */
    public function delete($id)
    {
        try {
            db('user')->where('id', $id)->delete();
        } catch (PDOException $e) {
        } catch (Exception $e) {
        }
    }
    /**
     * @param $id
     * @throws Exception
     * @throws PDOException
     */
    public function roleDelete($id)
    {
        db('role')->where('id', $id)->delete();
    }
    /**
     * @param $id
     * @throws Exception
     * @throws PDOException
     */
    public function ruleDelete($id)
    {
        db('rule')->where('id', $id)->delete();
    }

    public function stop($id)
    {
        db('role')->where('id',$id)->setField('status',1);
    }
    public function start($id)
    {
        db('role')->where('id',$id)->setField('status',0);
    }
}
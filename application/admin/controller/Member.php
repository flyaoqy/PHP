<?php

namespace app\admin\controller;
use app\admin\common\Base;
use think\Request;
use app\admin\model\Member as MemberModel;
//use think\Config;
use think\Cache;
class Member extends Base
{
    /**
     * 显示会员列表
     *
     * @return \think\Response
     * @throws \think\exception\DbException
     */
    public function index()
    {
		//获取数据库member表中的所有数据
		$member = db('member')->paginate(10,false,[
		     'type' => 'bootstrap',
		     'var_page' => 'page',
		]);
		$count = MemberModel::count();
		$this->assign('member',$member);//member为数据模板变量
		$this->assign('count',$count);
		//*重新登陆，cookie会重新刷新
	    //Cookie::set('name','admin888',array('expire'=>200,'prefix'=>'think_'));
		//var_dump($_COOKIE);
		//Cookie('think_name',null);
		//渲染模板
        return $this->fetch('member_list');
		
    }
	public function member()
    {
        return $this->fetch('member_del');
		
    }

    /**
     * 显示创建资源表单页.
     *
     * @param Request $request
     * @return \think\Response
     */
    public function create(Request $request)
    {
        $mca = $request->module().'/'.$request->controller().'/'.$request->action();
        if(in_array($mca,parent::certify())){
             return $this->fetch('member_add');
        } else {
            $this->error('无操作权限','index','',3);
        }
    }

    /**
     * @param $code
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function valid($code)
    {
        $checkCode= db('member')->where('code',$code)->find();
        if(!empty($checkCode)){
            $result = "商户代码已存在!";
		    return $result;
        } else if(empty($checkCode)){
            $result = "";
		    return $result;
        }
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request $request
     * @return void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function save(Request $request)
    {
        if($request->isPost()){
			$data = $request->param(true);//true包含文件
			
		} else {
			$this->error('请求类型错误~~');
			
		}
        if (!empty($data['code'])) {
            $where = db('member')->where('code',$data['code'])->find();
        }
        if(empty($where)){
		$data['status'] = 0;
		$res = MemberModel::create($data);
		if(is_null($res)){
			$this->error('新增失败','index',3);
		}
		$this->success('新增成功','index',3);
		//关闭当前弹出的子菜单（默认是3秒自动关闭）
        } else {
            $this->error('数据库已存在该合同编号，请修改!','index',3);
        }
		
    }

    /**
     * 显示指定的资源
     *
     * @return \think\Response
     * @throws \think\exception\DbException
     */
     public function search()
    {
      $company_name = input('company_name');
	  $where['company_name'] = array('like','%'.$company_name.'%');
	  //paginate的第三个参数：
      $member = MemberModel::where($where)->paginate(10,false,['query'=> request()->param()]);
	  $count = MemberModel::where($where)->count();
      $this->view->assign('member',$member);
      $this->view->assign('count',$count);
      
      return $this->fetch('member_list');
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param Request $request
     * @param  int $id
     * @return \think\Response
     * @throws \think\exception\DbException
     */
    public function edit(Request $request,$id)
    {
        $mca = $request->module().'/'.$request->controller().'/'.$request->action();
        if(in_array($mca,parent::certify())){
            $data = MemberModel::get($id);
            $this->view->assign('data',$data);
            return $this->fetch('member_edit');
        } else {
            $this->error('无操作权限','index','',3);
        }
    }

    /**
     * 保存更新的资源
     *
     * @return void
     */
    public function update()
    {
		//获取提交过来的所有数据包括文件
        $data = $this->request->param(true);
		$res = MemberModel::update([
		   'code'=>$data['code'],
		   'company_name'=>$data['company_name'],
		   'area'=>$data['area'],
		   'invoice_type'=>$data['invoice_type'],
		   'enterprise_type'=>$data['enterprise_type'],
		   'artificial_person'=>$data['artificial_person'],
		   'credit_code'=>$data['credit_code'],
		   'registered_address'=>$data['registered_address'],
		   'contacts'=>$data['contacts'],
		   'office_address'=>$data['office_address'],
		   'telno1'=>$data['telno1'],
		   'telno2'=>$data['telno2'],
		   'brand'=>$data['brand'],
		   'cheque'=>$data['cheque'],
		   'taxable_type'=>$data['taxable_type'],
		   'tax_rate'=>$data['tax_rate'],
		   'status'=>0
		],['id'=>$data['id']]);
        if(is_null($res)){
			$this->error('更新失败','index',3);
			
		}
			$this->success('更新成功','index',3);
    }

    /**
     * 删除指定资源
     *
     * @param Request $request
     * @param  int $id
     * @return void
     */
    public function delete(Request $request,$id)
    {
        $mca = $request->module().'/'.$request->controller().'/'.$request->action();
        if(in_array($mca,parent::certify())){
            MemberModel::destroy($id);
        } else {
            $this->error('无操作权限','index','',3);
        }
    }
	
	public function stop($id)
    {
        db('member')->where('id',$id)->setField('status',1);
    }
	public function start($id)
    {
        db('member')->where('id',$id)->setField('status',0);
    }
}
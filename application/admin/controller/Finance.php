<?php

namespace app\admin\controller;
use app\admin\common\Base;
use think\Request;

class Finance extends Base
{
    /**
     * 显示会员列表
     *
     * @return \think\Response
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function index()
    {
		//获取数据库account_receivable表中的所有数据
		$account_receivable = db('store_contract')
		                ->alias('s')
						->join('account_receivable a','s.contract_id=a.contract_id')
						->order('a.id asc')
		                ->paginate(10,false,[
		               'type' => 'bootstrap',
		               'var_page' => 'page',
		]);
		$count = db('account_receivable')
		                ->alias('a')
						->join('store_contract s','a.contract_id=s.contract_id')
						->count();
		//模板赋值
		$this->assign('account_receivable',$account_receivable);//account_receivable为数据模板变量
		$this->assign('count',$count);
		//渲染模板
        return $this->fetch('finance_list');
    }

    /**
     * @return mixed
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function check()
    {
        //获取数据库account_receivable表中的所有数据
        $account_check = db('store_contract')
            ->alias('s')
            ->join('account_check a','s.contract_id=a.contract_id')
            ->order('a.id asc')
            ->paginate(10,false,[
                'type' => 'bootstrap',
                'var_page' => 'page',
            ]);
        $count = db('account_check')
            ->alias('a')
            ->join('store_contract s','a.contract_id=s.contract_id')
            ->count();
        $fee= db('account_check')
            ->sum('received_price');
        //模板赋值
        $this->assign('account_check',$account_check);//account_receivable为数据模板变量
        $this->assign('count',$count);
        $this->assign('fee',$fee);
        //渲染模板
        return $this->fetch('finance_check');
    }
    /**
     * @return mixed
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function show()
    {
		$service_receive = db('store_contract')
		                ->alias('s')
						->join('service_receive a','s.contract_id=a.contract_id')
						->order('a.id asc')
		                ->paginate(10,false,[
		               'type' => 'bootstrap',
		               'var_page' => 'page',
		]);
		$count = db('service_receive')
		                ->alias('a')
						->join('store_contract s','a.contract_id=s.contract_id')
						->count();
		//模板赋值
		$this->assign('service_receive',$service_receive);//account_receivable为数据模板变量
		$this->assign('count',$count);
		//渲染模板
        return $this->fetch('finance_show');
    }

    /**
     * @param Request $request
     * @return string
     * @throws \think\Exception
     */
    public function create(Request $request)
    {
        $mca = $request->module().'/'.$request->controller().'/'.$request->action();
        if(in_array($mca,parent::certify())){
            return $this->view->fetch('finance_add');
        } else {
            $this->error('无操作权限','index','',3);
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return string
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function edit(Request $request,$id)
    {
        $mca = $request->module().'/'.$request->controller().'/'.$request->action();
        if(in_array($mca,parent::certify())){
            $data = db('account_receivable')->where('id',$id)->find();
            $this->view->assign('data',$data);
            return $this->view->fetch('finance_edit');
        } else {
            $this->error('无操作权限','index','',3);
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return string
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function compile(Request $request,$id)
    {
        $mca = $request->module().'/'.$request->controller().'/'.$request->action();
        if(in_array($mca,parent::certify())){
            $data = db('service_receive')->where('id',$id)->find();
            $this->view->assign('data',$data);
            return $this->view->fetch('finance_compile');
        } else {
            $this->error('无操作权限','index','',3);
        }
    }

    /**
     * @return mixed
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function select()
    {   
	    $start_date = input('start_date');
        $brand = input('brand');
		$status = input('status');
		if($start_date){
		    $where['start_date'] = array('<=',$start_date);
			$where['end_date'] = array('>=',$start_date);
		} else {
			$where['start_date'] =  array('>=','2000-01-01');
			$where['end_date'] = array('<=','2099-12-31');
		}
		$where['brand'] = array('like','%'.$brand.'%');
		$where['status'] = array('like','%'.$status.'%');
	
	  //  dump($where);die;
	    //paginate的第三个参数：
		$service_receive = db('store_contract')
		                ->alias('s')
						->join('service_receive a','s.contract_id=a.contract_id')
						->where([
						'a.start_date'=>$where['start_date'],
						'a.end_date'=>$where['end_date'],
						'a.status'=>$where['status'],
						's.brand'=>$where['brand']
						])
						->order('a.id asc')
		                ->paginate(10,false,['query'=> request()->param()]);
	    $count = db('store_contract')
		                ->alias('s')
						->join('service_receive a','s.contract_id=a.contract_id')
		                ->where([
                        'a.start_date'=>$where['start_date'],
						'a.end_date'=>$where['end_date'],
						'a.status'=>$where['status'],
						's.brand'=>$where['brand']
						])
		                ->count();
        $this->view->assign('service_receive',$service_receive);
        $this->view->assign('count',$count);
        return $this->fetch('finance_show');
    }

    /**
     * @return mixed
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function search()
    {   
	    $brand = input('brand');
	    $start_date = input('start_date');
		$status = input('status');
		if($start_date){
		    $where['start_date'] = array('<=',$start_date);
			$where['end_date'] = array('>=',$start_date);
		} else {
			$where['start_date'] =  array('>=','2000-01-01');
			$where['end_date'] = array('<=','2099-12-31');
		}
		$where['brand'] = array('like','%'.$brand.'%');
		$where['status'] = array('like','%'.$status.'%');
	    //paginate的第三个参数：
		$account_receivable = db('store_contract')
		                ->alias('s')
						->join('account_receivable a','s.contract_id=a.contract_id')
						->where([
						'a.start_date'=>$where['start_date'],
						'a.end_date'=>$where['end_date'],
						'a.status'=>$where['status'],
						's.brand'=>$where['brand']
						])
						->order('a.id asc')
		                ->paginate(10,false,['query'=> request()->param()]);
	    $count = db('store_contract')
		                ->alias('s')
						->join('account_receivable a','s.contract_id=a.contract_id')
		                ->where([
                        'a.start_date'=>$where['start_date'],
						'a.end_date'=>$where['end_date'],
						'a.status'=>$where['status'],
						's.brand'=>$where['brand']
						])
		                ->count();
        $this->view->assign('account_receivable',$account_receivable);
        $this->view->assign('count',$count);
        return $this->fetch('finance_list');
    }

    /**
     * @return mixed
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function query()
    {
        $brand = input('brand');
        $type = input('type');
        $direction = input('direction');
        $where['brand'] = array('like','%'.$brand.'%');
        $where['type'] = array('like','%'.$type.'%');
        $where['direction'] = array('like','%'.$direction.'%');
        $account_check = db('store_contract')
            ->alias('s')
            ->join('account_check a','s.contract_id=a.contract_id')
            ->where([
                's.brand'=>$where['brand'],
                'a.type'=>$where['type'],
                'a.direction'=>$where['direction']
            ])
            ->order('a.id asc')
            ->paginate(10,false,['query'=> request()->param()]);
        $count = db('store_contract')
            ->alias('s')
            ->join('account_check a','s.contract_id=a.contract_id')
            ->where([
                's.brand'=>$where['brand'],
                'a.type'=>$where['type'],
                'a.direction'=>$where['direction']
            ])
            ->count();
        $fee = db('store_contract')
            ->alias('s')
            ->join('account_check a','s.contract_id=a.contract_id')
            ->where([
                's.brand'=>$where['brand'],
                'a.type'=>$where['type'],
                'a.direction'=>$where['direction']
            ])
            ->sum('received_price');
        $this->view->assign('account_check',$account_check);
        $this->view->assign('count',$count);
        $this->view->assign('fee',$fee);
        return $this->fetch('finance_check');
    }

    /**
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function update()
    {
        		//获取提交过来的所有数据包括文件
        $data = $this->request->param(true);
        $select=db('account_receivable')
                  ->where('id',$data['id'])
                  ->find();
        db('account_receivable')
		          ->where('id',$data['id'])
		          ->update([
				    'received_price'=>$select['received_price']+$data['received_price'],
				    'difference'=>$select['difference']-$data['received_price'],
					'real_date'=>$data['real_date'],
                    'status'=>$data['status'],
                    'operate_time'=>date('Y-m-d H:i:s',time())
		        ]);
        $account_check = db('account_check')
                          ->data(['start_date'=>$data['start_date'],'end_date'=>$data['end_date'],'type'=>'固定租金','direction'=>0,'received_price'=>$data['received_price'],'real_date'=>$data['real_date'],'operate_time'=>date('Y-m-d H:i:s',time()),'contract_id'=>$data['contract_id']])
                          ->insert();
		if(is_null($account_check)){
			$this->error('更新失败','index',3);
			
		} else{
			$this->success('更新成功','index',3);
		}
    }
	
     public function save(Request $request)
    {
		if($request->isPost()){
			 //true包含上传文件
			$data = $request->param(true);
			if($data['direction']==0){
			$data = [
			    'contract_id'=>$data['contract_id'],
				'start_date'=>$data['start_date'],
				'end_date'=>$data['end_date'],
                'type'=>$data['type'],
                'direction'=>$data['direction'],
				'receivable_price'=>$data['receivable_price'],
				'received_price'=>0,
				'derate'=>$data['derate'],
				'difference'=>$data['receivable_price'],
				'operate_time'=>date('Y-m-d H:i:s',time()),
				'status'=>0,
				'des3'=>$data['des3'],
			];
			$res = db('service_receive')->insert($data);
			if(is_null($res)){
			    $this->error('新增失败','index',3);
		    } else{
                $this->success('新增成功','index',3);
            }
			}else if($data['direction']==1){
                $account_check = db('account_check')
                    ->data(['start_date'=>$data['start_date'],'end_date'=>$data['end_date'],'type'=>$data['type'],'direction'=>$data['direction'],'received_price'=>$data['receivable_price'],'real_date'=>$data['real_date'],'operate_time'=>date('Y-m-d H:i:s',time()),'contract_id'=>$data['contract_id']])
                    ->insert();
                if(is_null($account_check)){
                    $this->error('新增失败','index',3);
                } else{
                    $this->success('新增成功','index',3);
                }
            }
		} else {
			$this->error('请求类型错误!','index',3);
		}
    }

    /**
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function upgrade()
    {
        		//获取提交过来的所有数据包括文件
        $data = $this->request->param(true);
        $select=db('service_receive')
            ->where('id',$data['id'])
            ->find();
		db('service_receive')
		          ->where('id',$data['id'])
		          ->update([
					'start_date'=>$data['start_date'],
                    'end_date'=>$data['end_date'],
					'received_price'=>$select['received_price']+$data['received_price'],
					'derate'=>$data['derate'],
				    'difference'=>$select['difference']-$data['received_price'],
					'real_date'=>$data['real_date'],
                    'operate_time'=>date('Y-m-d H:i:s',time()),
					'status'=>$data['status'],
					'des3'=>$data['des3']
		        ]);
        $account_check = db('account_check')
            ->data(['start_date'=>$data['start_date'],'end_date'=>$data['end_date'],'type'=>$data['type'],'direction'=>0,'received_price'=>$data['received_price'],'real_date'=>$data['real_date'],'operate_time'=>date('Y-m-d H:i:s',time()),'contract_id'=>$data['contract_id']])
            ->insert();
		if(is_null($account_check )){
			$this->error('更新失败','show',3);
		} else {
			$this->success('更新成功','show',3);
		}
    }
	
}
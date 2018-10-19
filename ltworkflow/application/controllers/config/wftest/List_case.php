<?
class List_case extends CI_Controller {
	
	function list_case() {
		parent::__construct();
	}
	
	function loadview() {
		$data = array();
		$this->load->view ( 'top/bootstrap_top');
		$this->load->view ( 'config/wftest/list_case', $data );
		$this->load->view ( 'top/foot' );
	}
	
	function query() {
		$sql = "select tc_uid dt_primary_id,tc_name,tc_joinmark,tc_updateuser,tc_updatetime,tc_discript
		 		from t_wf_testcase 
		".$this->whereSql();
			$orderSQL = ' order by tc_updatetime desc';
		$result = $this->simple_model->get_dt_pageresult ( $sql );
		//把数组以json格式输出
		echo json_encode ( $result );
	}
	
	function whereSql (){
		$where = " where 1=1 ";
		$test_name = request( 'test_name' );
		if($test_name != ''){
			$where .= " and tc_name like '".sqlFilter($test_name)."%' ";
		}
		return $where;
	}
	function delete(){
		
		$ids = request('ids');
		$this->db->trans_start();
		$sql = "delete from sias..t_wf_testcase where tc_uid = ".sqlFilter($ids,3);	
		$this->db->query($sql);
		$sql = "delete from sias..t_wf_testcase_result where tcr_pid = ".sqlFilter($ids,3);		
		$this->db->query($sql);
		$this->db->trans_complete();
		
		$response = array();
		$response['status'] = 'success';
		echo json_encode($response);
	}
}

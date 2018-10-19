<?php
class simple_model extends CI_Model {


    /**
     * 通过数据生成数据库插入语句
     * @param $array_list 插入数据库的数组
     * @param $db_name 数据库库名
     * @param $table_name 数据库表名
     * @return SQL-insert语句
     * @author zhouxiang
     */
    function simple_insert_string($array_list, $db_name, $table_name) {
        $column_str = "";
        $value_str = "";
        foreach ( $array_list as $key => $value ) {
            $value = $this->sql_filter($value);
            $column_str .= $key . ',';
            $value_str .= $value . ',';
        }
        $column_str = substr ( $column_str, 0, strlen ( $column_str ) - 1 );
        $value_str = substr ( $value_str, 0, strlen ( $value_str ) - 1 );
        $db_name != ""?".".$db_name:"";
        $insert_str = 'insert into ' . $db_name . $table_name . ' (' . $column_str . ') values (' . $value_str . ')';
        return $insert_str;
    }


    /**
     * 通过数据生成数据库更新语句
     * @param $array_list 插入数据库的数组
     * @param $db_name 数据库库名
     * @param $table_name 数据库表名
     * @param $key_col_name update语句中where条件列名，一般为主键
     * @return SQL-update语句
     * @author zhouxiang
     */
    function simple_update_string($array_list, $db_name, $table_name, $key_col_name) {
        $column_str = "";
        $where = "";
        foreach ( $array_list as $key => $value ) {     
            $value = $this->sql_filter($value);
            if ($key == $key_col_name) {
                $where = $key . '=' . $value;
            }else{
                $column_str .= $key . '=' . $value . ',';
            }
        }
        $column_str = substr ( $column_str, 0, strlen ( $column_str ) - 1 );
        $db_name != ""?".".$db_name:"";
        $update_str = 'update ' . $db_name . $table_name . ' set ' . $column_str . ' where ' . $where;
        return $update_str;
    }

    function sql_filter($value) {
        $except_array = array(
            'newid()',
        	'now()',
            'getdate()'
        );
        if(in_array(strtolower($value),$except_array)){
            return $value;
        }
        $value = $this->db->escape($value);
        return $value;
    }
 //由结果集返回数组
  function returnqueryArray($result, $blank_flag = false) {
    $result_data = array ();
    if ($blank_flag) {
      array_push ( $result_data, array ('id' => '', 'value' => '' ) );
    }
    if ($result->num_rows () > 0) {
      foreach ( $result->result_array () as $row ) {
        $data_row = array ();
        foreach ( $row as $key => $value ) {
                    $data_row [$key] = trim( htmlspecialchars($value) );
        }
        array_push ( $result_data, $data_row );
      }
    }
    return $result_data;
  }
/**
     * datatable的分页方法
     * @author zhouxiang
     */
    public function get_dt_pageresult($sql) {
      
      // 1.分页信息
      $iDisplayStart = request('iDisplayStart');
      $iDisplayLength = request('iDisplayLength');
      $page_size = $iDisplayLength;
      $page_index = ceil(($iDisplayStart+1)/$iDisplayLength);
      
      // 2.排序信息：要求页面列dt_sName属性与数据库列字段完全一致
      $orderby = $this->get_datatable_orderby();
      
      // 3.总数
      $count_sql = 'select count(1) total from (' . $sql . ') a';
		//echo $count_sql;
	  $resule = $this->db->query ( $count_sql );
	  $total = $resule->row ()->total;
      
      //4.当页数据
      $e_sql = $sql . " limit ".($page_index - 1) * $page_size.",".$page_size;
	  $query = $this->db->query ( $e_sql );

      $returnData = array();
      $returnData['data'] = $query->result_array();
      $returnData['iTotalRecords'] = $total;
      $returnData['iTotalDisplayRecords'] = $total;
      
      return $returnData;
      
   
    
    }

    /**
     * 根据datatable传入的参数获取orderby信息
     * @author zhouxiang
     */
    function get_datatable_orderby(){
      $count = request('iSortingCols');
      $orderby = ' order by ';
      $columnsArr = explode(',',request('sColumns'));
      for( $i = 0 ; $i < $count ; $i++){
        $iSortCol = request('iSortCol_'.$i);
        $sSortDir = request('sSortDir_'.$i);
        $orderby .= ($i == 0?'':',').$columnsArr[$iSortCol].' '.$sSortDir;
      }
      $tail_orderby = request('tail_orderby');
      if($tail_orderby != ''){
        $orderby .= ", $tail_orderby desc ";
      }
      return $orderby;
    }
}

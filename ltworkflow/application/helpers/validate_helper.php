<?php

/**
 * 表单验证类
 * 2014-6-18_zhouxiang 前后端统一验证
 * 相关文件：前端 - jq_validate.js 后台 - validate_helper.php
 * 参考效果：controller - term.php view - term.php 合同添加条款验证
 */
 
class validate_helper {

		/** 正则验证规则 */
		private static $validatorRules = array(
                'integer'       => array( 'valid' => '/^[0-9]+$/' , 'name' => '正整数' ),
                'float'       	=> array( 'valid' => '/^[0-9]+(.[0-9]+)?$/' , 'name' => '数字' ),
                'date'          => array( 'valid' => '/^[0-9]{4}-(\d{1,2})-(\d{1,2})$/' , 'name' => '日期' ),
                'date_month'    => array( 'valid' => '/^[0-9]{4}-(\d{1,2})$/' , 'name' => '日期年月' ),
				'email'			=> array( 'valid' => '/^(\w)+(\.\w+)*@(\w)+((\.\w{2,3}){1,3})$/' , 'name' => '邮箱地址' )
        );
		
		/** 
		 * 验证
		 * @param $validate_model 验证数组对象
		 * @param $instance 调用验证的controller的实例
		 * @return 验证结果 array('valid' => true|false , 'msg' => string)
		 * @author zhouxiang
		 */
		public static function validate($validate_model,$instance){
			$response = array();
			$valid = true;
			
			// 校验通用部分
			$common_table = $validate_model['common'];
			foreach($common_table as $key => $each){
				if(isset($each['no_php']) && $each['no_php'] === true){
					continue;
				}
				
				// 如果是数组类型
				if(isset($each['is_array']) && $each['is_array'] === true){
					$value_array = request($key);
					if($value_array){
						$each_response = validate_helper::validate_one($each,$value_array,$common_table);
						array_push($response,$each_response);
					}
				}else{
					$value = request($key);				
					$each_response = validate_helper::validate_one($each,array($value),$common_table);
					array_push($response,$each_response);
				}
			}
			
			// 	校验自定义部分
			// 	返回值 
			//	$response = array(
			// 		'name'	=> 'abc',
			//		'msg' 	=> array('1','2')
			//	);
			$special_table = $validate_model['special'];
			if($special_table){
				foreach($special_table as $func_name){
					$each_response = $instance->$func_name();
					array_push($response,$each_response);
				}
			}
			
			// 加工返回信息msg
			$msg = '';
			foreach($response as $each_res){
				$name = $each_res['name'];
				$msg_arr = $each_res['msg'];
				if(count($msg_arr) != 0){
					foreach($msg_arr as $each_msg)
					$msg .= $name.'#th#'.$each_msg.'#td#';
				}
			}
			
			$result = array(
				'valid' => ($msg == ''),
				'msg' => $msg
			);
			
			return $result;
		}

		/** 
		 * 对于每一条的验证
		 * @param $each 验证单个对象
		 * @param $value_array 验证规则数组
		 * @param $data_table 验证数据表
		 * @return 验证结果 array('msg' => string)
		 * @author zhouxiang
		 */
		private static function validate_one($each,$value_array,$data_table){
			
			$response = array();
			$msg = array();
			$response['name'] = $each['describe'];
			
			
			foreach($value_array as $value){
			
				if( (isset($each['empty']) && $each['empty'] === true) && $value == '' ){
					continue;
				}
				
				// 验证非空 - 通用验证
				if($value == ''){
					validate_helper::do_array_push($msg,'不能为空');
				}
				
				// 最小长度验证
				$str_length = mb_strlen($value,'utf8');
				if(isset($each['minlength'])){
					$min_length = $each['minlength'];
					if($str_length < $min_length){
						validate_helper::do_array_push($msg,'长度不能小于 '.$min_length);
					}
				}
				// 最大长度验证
				if(isset($each['maxlength'])){
					$max_length = $each['maxlength'];
					if($str_length > $max_length){
						validate_helper::do_array_push($msg,'长度不能大于 '.$max_length);
					}
				}
				// 浮点数校验
				if(isset($each['floatlength'])){
					$float_length = $each['floatlength'];
					$float_reg = '/^[0-9]+(.[0-9]{1,'.$float_length.'})?$/';
					if(!preg_match($float_reg,$value)){
						validate_helper::do_array_push($msg,'小数位数不能超过 '.$float_length.' 位');
					}
				}
				// 比较规则
				if(isset($each['less_than'])){
					$larger_name = $data_table[$each['less_than']]['describe'];
					$larger_value = request($each['less_than']);
					if($value > $larger_value){
						validate_helper::do_array_push($msg,'不能大于 '.$larger_name);
					}
				}
				// 正则类型验证
				if(isset($each['type'])){
					$rule = validate_helper::$validatorRules[$each['type']];
					if(!preg_match($rule['valid'],$value)){
						validate_helper::do_array_push($msg,'只能是 '.$rule['name']);
					}
				}
			}
			$response['msg'] = $msg;
			return $response;
		}
		
		/** 
		 * 加入数组，排重
		 * @param $each 验证单个对象
		 * @param &$array 验证结果数组
		 * @param $element 单个验证结果
		 * @return null
		 * @author zhouxiang
		 */
		private static function do_array_push(&$array,$element){
			if(!in_array($element,$array)){
				array_push($array,$element);
			}
		}
		
}

?>
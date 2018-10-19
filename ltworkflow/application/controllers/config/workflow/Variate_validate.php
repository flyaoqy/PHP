<?php

class Variate_validate {

	public function get_validate(){
		
			$validators = array(
				'common' => array(
					'wf_uid' => array(
						'describe' => 'wf_uid',
					),
					'expression_key' => array(
						'describe' => '变量',
					)
				),
				'special' => array()
			);


		return $validators;
		
	}
}

?>
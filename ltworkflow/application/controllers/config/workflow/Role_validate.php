<?php

class Role_validate {

	public function get_validate(){
		
			$validators = array(
				'common' => array(
					'expression' => array(
						'describe' => '角色名称',
					)

				),
				'special' => array()
			);


		return $validators;
		
	}
}

?>
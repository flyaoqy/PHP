<?php
class is_login{
	var $CI;
	function __construct()
	{
	   $this->CI=&get_instance();
	}
	function check(){
		if ( preg_match("/purchase*/i", uri_string())){
			
			check_login();
			
		}
	}
}
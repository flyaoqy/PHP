<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


//数据库空值处理
if (! function_exists ( 'sqlFilter' )) {
	/*
	 * $r 1--id处理,转化为空   2--数字处理，空时补0  3--字符串处理，空时补‘’
	 */
function sqlFilter($value, $r = 0) {
		if ($r == 1) {
			if (isNull ( $value )) {
				$value =  'null';
			}else{
				$value = str_replace ( "'" , "''" , $value );
				$value = "'" . $value . "'";
			}
		}elseif ($r == 2) {
			if (isNull ( $value )) {
				$value = '0';
			}else{
				$value = replaceComma ( $value );
				if(!is_numeric($value)){
					$value = '0';
				}
			}
		}elseif ($r == 3) {
			if (isNull ( $value )) {
				$value =  "''";
			}else{
				$value = str_replace ( "'" , "''" , $value );
				$value =  "'" . $value . "'";
			}
		}else{
			if (isNull ( $value )) {
				return null;
			}
			$value = str_replace ( "'" , "''" , $value );
		}
		return $value;
	}
}


if (!function_exists('request'))
{
	function request($name)
	{
		
		$value = isset($_GET[$name])?$_GET[$name]:"";
        if(isNull($value)){
          $value = isset($_POST[$name])?$_POST[$name]:"";
		}
		if(isNull($value)){
		  $value = isset($_REQUEST[$name])?$_REQUEST[$name]:"";
		}
        return trim($value);
		//return site_addslashes($value);
	}
}

if (!function_exists('isNull'))
{
	function isNull($value) {
		
		if (is_numeric ( $value )) {
			return false;
		}
		if ($value == null || is_string ( $value ) && trim ( $value ) == '') {
			return true;
		
		} else if (is_array ( $value ) && count ( $value ) == 0) {
			return true;
		}
		
		return false;
	}
}

if(!function_exists('uuid'))
{
   function uuid($prefix = '')   
  {   
    $chars = md5(uniqid(mt_rand(), true));   
    $uuid  = substr($chars,0,8) . '-';   
    $uuid .= substr($chars,8,4) . '-';   
    $uuid .= substr($chars,12,4) . '-';   
    $uuid .= substr($chars,16,4) . '-';   
    $uuid .= substr($chars,20,12);   
    return $prefix . $uuid;   
  }  
}


if (! function_exists ( 'check_login' )) {
	function check_login() {
		$ssn = $_SESSION['ssn'];
		if(isNull($ssn)){
			redirect(site_url('login'));
			exit();
		}
	}
}

//替换字符串中的,和，
if (! function_exists ( 'replaceComma' )) {
	function replaceComma($value) {
		$value = str_replace ( ',', '', $value );
		$value = str_replace ( '，', '', $value );
		return $value;
	}
}
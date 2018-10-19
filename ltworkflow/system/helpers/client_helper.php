<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 获取客户端信息，限制操作等
 * @author		liutao
 */

// ------------------------------------------------------------------------
/**
 * 
 * 防止客户端重复提交
 */
if ( ! function_exists('prevent_repeat_submit'))
{
	function prevent_repeat_submit(){
		
//		if(isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"])=="xmlhttprequest"){ 
//		    // ajax 请求的处理方式 
//		    writeLog("ajax");
//		}else{ 
//		    // 正常请求的处理方式 
//		     writeLog("numall");
//		};
	    $allowTime = 5; //限定单位时间
	    $allowNum = 2;  //单位时间允许访问次数
	    $uri = @$_SERVER['HTTP_HOST'].@$_SERVER['PHP_SELF'];
	    $get = implode(",",array_values($_GET)) ;
	    $allowKey = md5($uri . $get);  
	    //添加用于校验重复提交的session
	    if (!isset($_SESSION['check_repeat_submit'])) {
	    	$_SESSION['check_repeat_submit'] = array();
	    }
	    //添加校验key 
	    if (!array_key_exists($allowKey,$_SESSION['check_repeat_submit'])) {
	    	$accessMark['time'] = time();
	    	$accessMark['num'] = 1;//访问次数
	    	$_SESSION['check_repeat_submit'][$allowKey] = $accessMark; 
	    }else{
	    	$accessMark = $_SESSION['check_repeat_submit'][$allowKey];
		    $interval = time() - $accessMark['time']; //计算间隔时间（秒）
		    if($interval < $allowTime){//小于单位时间
		    	$accessMark['num']++;
		    	//writeLog("accessMark".$accessMark['num']);
		    	if($accessMark['num'] > $allowNum){//大于允许访问次数，程序挂起到单位时间
		    		 //writeLog($uri." sleep ".($allowTime - $interval));
		    		 sleep($allowTime - $interval);
		    		 $accessMark['time'] = time();
		    		 $accessMark['num'] = 1;//访问次数
		    		 $_SESSION['check_repeat_submit'][$allowKey] = $accessMark;  
		    	}else{//小于允许访问次数，访问次数加一
		    		 $_SESSION['check_repeat_submit'][$allowKey] = $accessMark;  
		    	}
		    }else{//大于单位时间，重置访问时间、访问次数
			    $accessMark['time'] = time();
		    	$accessMark['num'] = 1;//访问次数
		    	//writeLog("reset accessMark".$accessMark['num']);
		    	$_SESSION['check_repeat_submit'][$allowKey] = $accessMark;  
		    }
	    } 
	    session_write_close();//立即写入session
	}
}
/**
 * 
 * 获取IP地址（摘自discuz）
 */
if ( ! function_exists('get_ip'))
{
	function get_ip(){
		$ip='';
		if(!empty($_SERVER['HTTP_CLIENT_IP'])){
			return is_ip($_SERVER['HTTP_CLIENT_IP'])?$_SERVER['HTTP_CLIENT_IP']:$ip;
		}elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
			return is_ip($_SERVER['HTTP_X_FORWARDED_FOR'])?$_SERVER['HTTP_X_FORWARDED_FOR']:$ip;
		}else{
			return is_ip($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:$ip;
		}
	}
}
/**
 * 
 * 判断是否为ip 地址
 * @param $str
 */
if ( ! function_exists('is_ip'))
{
	function is_ip($str){
		$ip=explode('.',$str);
		for($i=0;$i<count($ip);$i++){  
			if($ip[$i]>255){  
				return false;  
			}  
		}  
		return preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/',$str);  
	}
}
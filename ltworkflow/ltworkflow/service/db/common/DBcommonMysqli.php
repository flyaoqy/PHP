<?php
namespace com\ltworkflow;
/**
 * 
 * mysqli 数据库操作
 * @author liutao
 *
 */
class DBcommonMysqli{
	public $username;
	public $password;
	public $hostname;
	public $database;
	public $dbdriver		= 'mysqli';
	public $dbprefix		= '';
	public $char_set		= 'utf8';
	public $dbcollat		= 'utf8_general_ci';
	public $autoinit		= TRUE; // Whether to automatically initialize the DB
	public $port			= '';
	public $conn_id		= FALSE;
	public $debug          = FALSE;
	

	/**
	 * Compression flag
	 *
	 * @var	bool
	 */
	public $compress = FALSE;

	/**
	 * DELETE hack flag
	 *
	 * Whether to use the MySQL "delete hack" which allows the number
	 * of affected rows to be shown. Uses a preg_replace when enabled,
	 * adding a bit more processing to all queries.
	 *
	 * @var	bool
	 */
	public $delete_hack = TRUE;

	/**
	 * Strict ON flag
	 *
	 * Whether we're running in strict SQL mode.
	 *
	 * @var	bool
	 */
	public $stricton;

	// --------------------------------------------------------------------

	/**
	 * Identifier escape character
	 *
	 * @var	string
	 */
	protected $_escape_char = '`';
	
	/**
	 * MySQLi object
	 *
	 * Has to be preserved without being assigned to $conn_id.
	 *
	 * @var	MySQLi
	 */
	protected $_mysqli;
	
	public function __construct($params){
		//将配置放到本类中
		foreach ($params as $key => $val){
			$this->$key = $val;
		}
		//连接数据库
		$this->conn_id =  $this->db_connect();
		if ( ! $this->conn_id)
		{
			$this->log_message('error', 'Unable to connect to the database');
			return FALSE;
		}
		$this->db_select();//数据库选择
		$this->db_set_charset();
		
	}
	/**
	 * 
	 * 查询结果集或执行sql
	 * @param unknown_type $sql
	 * @param unknown_type $conn_id
	 */
	public function query($sql){
		if($this->debug){
			$this->log_message('error'," debug ".$sql);
		}
		//如果是执行sql 返回执行方法
		if ($this->is_write_type($sql) === TRUE)
		{
			return $this->exec( $sql );
		}
		//执行sql
		$query =$this->conn_id->query($this->_prep_query($sql));
		if($query === false){//执行语句错误
			$this->log_message('error',$sql);
			return false;
		}
		//查询结果集
		$result = array();
		while($row = mysqli_fetch_assoc($query)){
			array_push($result, $row);
		}
		return new DBCommonResult($result);
	}
	/**
	 * 
	 * 执行sql
	 * @param unknown_type $sql
	 * @param unknown_type $conn_id
	 */
	public function exec($sql){
		if($this->debug){
			$this->log_message('error'," debug ".$sql);
		}
		//执行sql
		$query = $this->conn_id->query($this->_prep_query($sql));;
		if($query === false){//执行语句错误
			$this->log_message('error',$sql);
			return false;
		}
		return true;
	}
	/**
	 * Prep the query
	 *
	 * If needed, each database adapter can prep the query string
	 *
	 * @param	string	$sql	an SQL query
	 * @return	string
	 */
	protected function _prep_query($sql)
	{
		// mysqli_affected_rows() returns 0 for "DELETE FROM TABLE" queries. This hack
		// modifies the query so that it a proper number of affected rows is returned.
		if ($this->delete_hack === TRUE && preg_match('/^\s*DELETE\s+FROM\s+(\S+)\s*$/i', $sql))
		{
			return trim($sql).' WHERE 1=1';
		}

		return $sql;
	}
	/**
	 * 
	 * 执行行数
	 */
	public function rows_affected() { 
		return $this->conn_id->affected_rows;
	}
	/**
	 * 
	 * 错误信息
	 */
	public function get_last_message(){
		if ( ! empty($this->_mysqli->connect_errno))
		{
			return array(
				'code'    => $this->_mysqli->connect_errno,
				'message' => $this->_mysqli->connect_error
			);
		}

		return array('code' => $this->conn_id->errno, 'message' => $this->conn_id->error);
	}
	/**
	 * 
	 * 事务开启
	 */
	public function begin_tran() {
		$this->conn_id->autocommit(FALSE);
		if(version_compare(PHP_VERSION, '5.5', '>=')){
		    return $this->conn_id->begin_transaction();
        }else{
            wf_debug("START TRANSACTION");
            return $this->simple_query('START TRANSACTION');
        }

		//return is_php('5.5')
		//	? $this->conn_id->begin_transaction()
		//	: $this->simple_query('START TRANSACTION'); // can also be BEGIN or BEGIN WORK
	}
	/**
	 * 
	 * 提交结束
	 * @param unknown_type $flag
	 */
	public function commit_tran() {
		if ($this->conn_id->commit())
		{
			$this->conn_id->autocommit(TRUE);
			return TRUE;
		}

		return FALSE;
	}
	/**
	 * 
	 * 回滚结束
	 * @param unknown_type $flag
	 */
	public function rollback_tran() {


		if ($this->conn_id->rollback())
		{
			$this->conn_id->autocommit(TRUE);
			return TRUE;
		}

		return FALSE;
	}
	/**
	 * 
	 * 关闭
	 */
	public function  close()
	{
		$this->conn_id->close();
	}
	/**
	 * Non-persistent database connection
	 *
	 * @access	private called by the base class
	 * @return	resource
	 */
	public function db_connect($persistent = FALSE)
	{
		// Do we have a socket path?
		if ($this->hostname[0] === '/')
		{
			$hostname = NULL;
			$port = NULL;
			$socket = $this->hostname;
		}
		else
		{
			$hostname = ($persistent === TRUE)
				? 'p:'.$this->hostname : $this->hostname;
			$port = empty($this->port) ? NULL : $this->port;
			$socket = NULL;
		}

		$client_flags = ($this->compress === TRUE) ? MYSQLI_CLIENT_COMPRESS : 0;
		$this->_mysqli = mysqli_init();

		$this->_mysqli->options(MYSQLI_OPT_CONNECT_TIMEOUT, 10);

		if (isset($this->stricton))
		{
			if ($this->stricton)
			{
				$this->_mysqli->options(MYSQLI_INIT_COMMAND, 'SET SESSION sql_mode = CONCAT(@@sql_mode, ",", "STRICT_ALL_TABLES")');
			}
			else
			{
				$this->_mysqli->options(MYSQLI_INIT_COMMAND,
					'SET SESSION sql_mode =
					REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(
					@@sql_mode,
					"STRICT_ALL_TABLES,", ""),
					",STRICT_ALL_TABLES", ""),
					"STRICT_ALL_TABLES", ""),
					"STRICT_TRANS_TABLES,", ""),
					",STRICT_TRANS_TABLES", ""),
					"STRICT_TRANS_TABLES", "")'
				);
			}
		}

		if ($this->_mysqli->real_connect($hostname, $this->username, $this->password, $this->database, $port, $socket, $client_flags))
		{
			// Prior to version 5.7.3, MySQL silently downgrades to an unencrypted connection if SSL setup fails
			if (
				($client_flags & MYSQLI_CLIENT_SSL)
				&& version_compare($this->_mysqli->client_info, '5.7.3', '<=')
				&& empty($this->_mysqli->query("SHOW STATUS LIKE 'ssl_cipher'")->fetch_object()->Value)
			)
			{
				$this->_mysqli->close();
				$message = 'MySQLi was configured for an SSL connection, but got an unencrypted connection instead!';
				log_message('error', $message);
				return ($this->db_debug) ? $this->display_error($message, '', TRUE) : FALSE;
			}

			return $this->_mysqli;
		}

		return FALSE;
	}
	/**
	 * Reconnect
	 *
	 * Keep / reestablish the db connection if no queries have been
	 * sent for a length of time exceeding the server's idle timeout
	 *
	 * @return	void
	 */
	public function reconnect()
	{
		if ($this->conn_id !== FALSE && $this->conn_id->ping() === FALSE)
		{
			$this->conn_id = FALSE;
		}
	}
	/**
	 * Select the database
	 *
	 * @access	private called by the base class
	 * @return	resource
	 */
	public function db_select($database = '')
	{
		if ($database === '')
		{
			$database = $this->database;
		}

		if ($this->conn_id->select_db($database))
		{
			$this->database = $database;
			return TRUE;
		}

		return FALSE;
	}
	/**
	 * Set client character set
	 *
	 * @param	string	$charset
	 * @return	bool
	 */
	public function db_set_charset($charset = '')
	{
		if ($charset === '')
		{
			$charset = $this->char_set;
		}
		
		return $this->conn_id->set_charset($charset);
	}
	
	/**
	 * Determines if a query is a "write" type.
	 *
	 * @access	public
	 * @param	string	An SQL query string
	 * @return	boolean
	 */
	private function is_write_type($sql)
	{
		if ( ! preg_match('/^\s*"?(SET|INSERT|UPDATE|DELETE|REPLACE|CREATE|DROP|TRUNCATE|LOAD DATA|COPY|ALTER|GRANT|REVOKE|LOCK|UNLOCK)\s+/i', $sql))
		{
			return FALSE;
		}
		return TRUE;
	}
	/**
	 * 
	 * 错误信息处理
	 * @param  $message
	 */
	private function log_message($level,$message){
		$sqlmsg = $this->get_last_message();
		wf_debug($message,$level);
		wf_debug($sqlmsg,$level);
	}
}
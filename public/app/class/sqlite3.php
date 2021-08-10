<?php
class SQLiteDB extends SQLite3 {
	function __construct($dbname){
		try {
			$this->open($dbname);
		}catch (Exception $e){
			die($e->getMessage());
		}
	}
}
 
class DBUtils {
	public static $db;

	public function instance($dbname){
		if (!self::$db) {
			self::$db = new SQLiteDB($dbname);
		}		
	}
 
	/**
	 * 创建表
	 * @param string $sql
	 */
	public static function create($sql){
		$result = @self::$db->query($sql);
		if ($result) {
			return true;
		}
		return false;
	}
 
	/**
	 * 执行增删改操作
	 * @param string $sql
	 */
	public static function execute($sql){
		$result = @self::$db->exec($sql);
		if ($result) {
			return true;
		}
		return false;
	}
 
	/**
	 * 获取记录条数
	 * @param string $sql
	 * @return int
	 */
	public static function count($sql){
		$result = @self::$db->querySingle($sql);
		return $result ? $result : 0;
	}
 
	/**
	 * 查询单个字段
	 * @param string $sql
	 * @return void|string
	 */
	public static function querySingle($sql){
		$result = @self::$db->querySingle($sql);
		return $result ? $result : '';
	}
 
	/**
	 * 查询单条记录
	 * @param string $sql
	 * @return array
	 */
	public static function queryRow($sql){
		$result = @self::$db->querySingle($sql,true);
		return $result;
	}
 
	/**
	 * 查询多条记录
	 * @param string $sql
	 * @return array
	 */
	public static function queryList($sql){
		$result = array();
		$ret = @self::$db->query($sql);
		if (!$ret) {
			return $result;
		}
		while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
			array_push($result, $row);
		}
		return $result;		
	}
}

<?php
/**
 * cookie class file
 * 用于cookie处理操作
 */

if (!defined('IN_FINECMS')) exit();

class cookie extends Fn_base {
	
	/**
	 * 判断cookie变量是否存在
	 * 
	 * @access public
	 * @param string $cookie_name	cookie的变量名
	 * @return boolean
	 */
	public static function is_set($cookie_name) {
		if (!$cookie_name) return false;
		$cookie_name = SYS_VAR_PREX . $cookie_name;
		return isset($_COOKIE[$cookie_name]);
	}
	
	/**
	 * 获取某cookie变量的值
	 * 
	 * 获取的数值是进过64decode解密的,注:参数支持数组
	 * @access public
	 * @param string $cookie_name	cookie变量名
	 * @return string
	 */
	public static function get($cookie_name) {
		if (!$cookie_name) return false;
		$cookie_name = SYS_VAR_PREX . $cookie_name;
		return isset($_COOKIE[$cookie_name]) ? unserialize(base64_decode($_COOKIE[$cookie_name])) : false;
	}
	
	/**
	 * 设置某cookie变量的值
	 * 
	 * 注:这里设置的cookie值是经过64code加密过的,要想获取需要解密.参数支持数组
	 * @access public
	 * @param string $name 		cookie的变量名
	 * @param string $value 	cookie值
	 * @param intger $expire	cookie所持续的有效时间,默认为一小时.(这个参数是时间段不是时间点,参数为一小时就是指从现在开始一小时内有效)
	 * @param string $path		cookie所存放的目录,默认为网站根目录
	 * @param string $domain	cookie所支持的域名,默认为空
	 * @return void	
	 */
	public static function set($name, $value, $expire = null, $path = null, $domain = null) {
		$name   = SYS_VAR_PREX . $name;
		$expire = is_null($expire) ? time()+3600 : time()+$expire;
		if (is_null($path)) $path = '/';
		if (is_null($domain)) $domain = SYS_COOKIE_DOMAIN;
		//数据加密处理.	
		$value = base64_encode(serialize($value));
		setcookie($name, $value, $expire, $path, $domain);
		$_COOKIE[$name] = $value;		
	}
	
	/**
	 * 删除某个Cookie值
	 * 
	 * @access public
	 * @param string $name	cookie的变量值
	 * @return void
	 */
	public static function delete($name) {
		self::set($name, '', '-3600');
		unset($_COOKIE[SYS_VAR_PREX . $name]);
	}
	
	/**
	 * 清空cookie
	 * 
	 * @access public
	 * @return void
	 */
	public static function clear() {
		unset($_COOKIE);
	}
}
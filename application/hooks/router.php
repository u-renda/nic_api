<?php if ( ! defined('BASEPATH')) exit ('No direct script access allowed');

class Router {
	
	private static $_format = '';
	
	public function route() 
	{
		$request = strstr($_SERVER['REQUEST_URI'], '?', true);
		
		if ( ! $request)
		{
			$request = $_SERVER['REQUEST_URI'];
		}
		
		$parts = explode('.', $request);
		self::$_format = $parts[sizeof($parts) - 1];
		
		if (self::$_format == 'json' || self::$_format == 'xml' || self::$_format == 'rss' || self::$_format == 'atom')
		{
			$_SERVER['REQUEST_URI'] = substr($request, 0, (strlen($request) - strlen(self::$_format) - 1));
		}
		else
		{
			self::$_format = '';
		}
	}
	
	public function config()
	{
		$CI =& get_instance();
		$CI->config->set_item('response_format', self::$_format);
	}
}

<?php

class Request {
	
	public static $get;
	public static $post;
	public static $request;
	public static $server;
	public static $env;
	
}

Request::$get = new Request_Filter($_GET);
Request::$post = new Request_Filter($_POST);
Request::$request = new Request_Filter($_REQUEST);
Request::$server = new Request_Server($_SERVER);
Request::$env = new Request_Filter($_ENV);

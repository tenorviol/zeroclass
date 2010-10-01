<?php

class Request {
	public static $_COOKIE;
	public static $_ENV;
	public static $_GET;
	public static $_POST;
	public static $_REQUEST;
	public static $_SERVER;
}

Request::$_COOKIE = new Request_Filter($_COOKIE);
Request::$_ENV = new Request_Filter($_ENV);
Request::$_GET = new Request_Filter($_GET);
Request::$_POST = new Request_Filter($_POST);
Request::$_REQUEST = new Request_Filter($_REQUEST);
Request::$_SERVER = new Request_Server($_SERVER);

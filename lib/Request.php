<?php

class Request {
	public static $COOKIE;
	public static $ENV;
	public static $GET;
	public static $POST;
	public static $REQUEST;
	public static $SERVER;
}

Request::$COOKIE = new Request_Filter($_COOKIE);
Request::$ENV = new Request_Filter($_ENV);
Request::$GET = new Request_Filter($_GET);
Request::$POST = new Request_Filter($_POST);
Request::$REQUEST = new Request_Filter($_REQUEST);
Request::$SERVER = new Request_Server($_SERVER);

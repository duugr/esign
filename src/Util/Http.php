<?php


namespace ESign\Util;


class Http
{

	public static function __callStatic($method, $parameters)
	{
		return (new static)->$method(...$parameters);
	}

	public function DoGet($url) {

		return '';
	}
	public function DoPost($url, $params) {

		return '';
	}
}
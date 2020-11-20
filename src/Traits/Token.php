<?php


namespace ESign\Traits;


use ESign\Util\Http;

trait Token
{
	public function GetToken() {
		$grantType      = "client_credentials";
		$arr            = [
			"appId"     => $this->appId,
			"secret"    => $this->secret,
			"grantType" => $grantType
		];
		$url = sprintf($this->getToken, $this->host, http_build_query($arr));

		$return_content = Http::DoGet($url);

		$result  = (array) json_decode($return_content, true);

		if ($result['code'] == 0 && isset($result['data']['token'])) {
			$this->token = $result['data']['token'];
		}
	}
}
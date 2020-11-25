<?php


namespace ESign\Traits;


use ESign\Urls;
use ESign\Util\Http;
use GuzzleHttp\Exception\RequestException;

trait Token
{
	public function GetToken() {
		$data = [
			'query' => [
				"appId"     => $this->appId,
				"secret"    => $this->secret,
				"grantType" => 'client_credentials'
			]
		];

		$response = $this->client->get(Urls::AccessToken, $data);
		if (is_bool($response)) {
			return $response;
		}
		$this->token = $response['token'];
		return $response['token'];
	}
}
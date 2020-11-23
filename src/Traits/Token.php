<?php


namespace ESign\Traits;


use ESign\Util\Http;
use GuzzleHttp\Exception\RequestException;

trait Token
{
	public function GetToken() {
		$data            = ['query' => [
			"appId"     => $this->appId,
			"secret"    => $this->secret,
			"grantType" => 'client_credentials'
		]];

		try {
			$response = $this->client->get($this->urlGetToken, $data);
			$body = $response->getBody()->getContents();

			$result  = (array) json_decode($body, true);

			if ($result['code'] == 0 && isset($result['data']['token'])) {
				$this->token = $result['data']['token'];
			} else {
				$this->errCode = $result['code'];
				$this->errMessage = $result['message'];
			}
		} catch (RequestException $e) {
			$this->errCode = $e->getCode();
			$this->errMessage = $e->getMessage();
		}

	}
}
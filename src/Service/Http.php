<?php


namespace ESign\Service;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Http
{
	protected $client;
	public $errCode;
	public $errMessage;

	public function __construct($host) {
		$this->client = new Client([
			// Base URI is used with relative requests
			'base_uri' => $host,
			// You can set any number of default request options.
			//			'timeout'  => 5.0,

		]);
	}

	public function get($uri, $data) {
		try {
			$response = $this->client->get($uri, $data);
			$body     = $response->getBody()->getContents();

			$result = (array) json_decode($body, true);

			if ($result['code'] == 0 && isset($result['data']['token'])) {
				return $result['data'];
			} else {
				$this->errCode    = $result['code'] ?? -1;
				$this->errMessage = $result['message'] ?? '';
			}
		} catch (RequestException $e) {
			$this->errCode    = $e->getCode();
			$this->errMessage = $e->getMessage();
		}
		return false;
	}

	public function delete($uri, $data) {
		try {
			$response = $this->client->delete($uri, $data);
			$body     = $response->getBody()->getContents();

			$result = (array) json_decode($body, true);

			if ($result['code'] == 0 && isset($result['data'])) {
				return true;
			} else {
				$this->errCode    = $result['code'] ?? -1;
				$this->errMessage = $result['message'] ?? '';
			}
		} catch (RequestException $e) {
			$this->errCode    = $e->getCode();
			$this->errMessage = $e->getMessage();
		}
		return false;
	}

	public function post($uri, $data) {
		try {
			$response = $this->client->post($uri, $data);
			$body     = $response->getBody()->getContents();

			$result = (array) json_decode($body, true);

			if ($result['code'] == 0 && isset($result['data'])) {
				return $result['data'];
			} else {
				$this->errCode    = $result['code'] ?? -1;
				$this->errMessage = $result['message'] ?? '';
			}
		} catch (RequestException $e) {
			$this->errCode    = $e->getCode();
			$this->errMessage = $e->getMessage();
		}

		return false;
	}
}
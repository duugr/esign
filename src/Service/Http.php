<?php

namespace ESign\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;

class Http
{
    protected $client;
    public    $errCode;
    public    $errMessage;

    public function __construct($host)
    {
        $this->client = new Client([
            // Base URI is used with relative requests
            'base_uri' => $host
            // You can set any number of default request options.
            //			'timeout'  => 5.0,

        ]);
    }

    /**
     * @param $uri
     * @param $data
     *
     * @return false|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getNoHeader($uri, $data)
    {
        try {
            $response = $this->client->get($uri, $data);
            $body     = $response->getBody()->getContents();

            $result = (array)json_decode($body, true);

            if (0 == $result['code'] && isset($result['data']['token'])) {
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

    /**
     * @param $uri
     * @param $data
     *
     * @return false|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get($uri, $data)
    {
        try {
            $response = $this->client->get($uri, $data);
            $body     = $response->getBody()->getContents();
            $result   = (array)json_decode($body, true);

            if (0 == $result['code'] && isset($result['data'])) {
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

    public function delete($uri, $data): bool
    {
        try {
            $header = $data['headers'];
            unset($data['headers']);
            if (isset($data['query'])) {
                $uri = sprintf("%s?%s", $uri, http_build_query($data['query']));
                unset($data['query']);
            }

            $request  = new Request('DELETE', $uri, $header, json_encode($data, JSON_UNESCAPED_UNICODE));
            $response = $this->client->send($request, ['timeout' => 2]);

            //			$response = $this->client->delete($uri, $data);
            $body = $response->getBody()->getContents();

            $result = (array)json_decode($body, true);

            if (0 == $result['code'] && isset($result['data'])) {
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

    /**
     * @param $uri
     * @param $data
     *
     * @return false|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function post($uri, $data)
    {
        try {
            $header = $data['headers'];
            unset($data['headers']);
            if (isset($data['query'])) {
                $uri = sprintf("%s?%s", $uri, http_build_query($data['query']));
                unset($data['query']);
            }

            $request  = new Request('POST', $uri, $header, json_encode($data, JSON_UNESCAPED_UNICODE));
            $response = $this->client->send($request, ['timeout' => 2]);

//            $response = $this->client->post($uri, $data);
            $body = $response->getBody()->getContents();

            $result = (array)json_decode($body, true);

            if (0 == $result['code'] && isset($result['data'])) {
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

    /**
     * @param $uri
     * @param $data
     *
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function put($uri, $data): bool
    {
        try {
            $response = $this->client->put($uri, $data);
            $body     = $response->getBody()->getContents();

            $result = (array)json_decode($body, true);

            if (0 == $result['code']) {
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

    /**
     * @param $uri
     * @param $data
     *
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function putContent($uri, $contentMd5, $filePath): bool
    {
        try {

            $body['body']    = fopen($filePath, 'r');
            $body['headers'] = [
                'stream'       => true,
                'Content-Type' => 'application/pdf',
                'Content-Md5'  => $contentMd5
            ];

            $response = $this->client->request('PUT', $uri, $body);

            if ($response->getStatusCode() == 200) {
                return true;
            } else {
                $this->errCode    = $response->getStatusCode() ?? -1;
                $this->errMessage = $response->getReasonPhrase() ?? $response->getBody() ?? '';
            }
        } catch (RequestException $e) {
            $this->errCode    = $e->getCode();
            $this->errMessage = $e->getMessage();
        }

        return false;
    }
}

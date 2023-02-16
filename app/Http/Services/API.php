<?php
namespace App\Http\Services;

use App\Traits\ApiResponse;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class API 
{
    private $API_URL;
    private $client;

    public function __construct(string $uri='', string $client_type='')
    {
        $this->API_URL = strlen($uri) == 0 ? env('VTPASS_API_URL') : $uri;
		
		if (empty($client_type)) {
			$this->client = new Client([
				'base_uri' => $this->API_URL,
				'headers'=>[
					'Authorization'=> "Basic " . base64_encode("sandbox@vtpass.com:sandbox")
				]
			]);
		} elseif ($client_type == 'paystack') {
			$this->client = new Client([
				'base_uri' => $this->API_URL,
				'headers'=>[
					'Authorization'=> "Bearer " . env('PAYSTACK_SECRET_KEY')
				]
			]);
		}
    }


	/**
	 * Default header for request
	 */
	protected static function header(): array
	{

		return [
			'Content-Type' => 'application/json',
		];
	}

    /**
	 * Formats data if form is multipart
	 */
	protected static function formatMultipartData($body): array
	{
		$options['multipart'] = $body['multipart'];
		$header = self::header();
		unset($header['Content-Type']);
		$options['headers'] = $header;

		return $options;
	}

    public function get(string $url, array $params = [], array $header = null): ApiResponse
    {
        try {
            $param = [
                'query' => $params
            ];

            $response = $this->client->get($url, $param);

            return new ApiResponse($response);
        } catch (GuzzleException $ex) {
            return new ApiResponse($ex);
        }
    }


    /**
	 * Submit post request to API
	 * @param string $path
	 * @param array $body
	 * @return ApiResponse
	 */
	public function post(string $path, array $body, array $header = null): ApiResponse
	{
		try {

			if (isset($body['multipart'])) {
				$options = self::formatMultipartData($body);
			} else {
				$options['json'] = $body;
				$options['headers'] = $header ?? self::header();
			}

			$response = $this->client->post($path, $options);
			return new ApiResponse($response);
		} catch (GuzzleException  $e) {
			return new ApiResponse($e);
		} catch (\Exception $e) {
			return new ApiResponse($e);
		}
	}
}
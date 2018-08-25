<?php

namespace BenMorel\LanguageLayer;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Client for the LanguageLayer API.
 */
class LanguageLayerClient
{
    private const ENDPOINT = 'http://apilayer.net/api/detect';

    /**
     * @var \GuzzleHttp\Client
     */
    private $httpClient;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * Class constructor.
     *
     * @param string $apiKey
     */
    public function __construct(string $apiKey)
    {
        $this->httpClient = new Client();
        $this->apiKey = $apiKey;
    }

    /**
     * @param string $text
     *
     * @return LanguageDetectionResult[]
     *
     * @throws LanguageDetectionException
     */
    public function detectLanguage(string $text) : array
    {
        try {
            $response = $this->httpClient->request('GET', self::ENDPOINT, [
                RequestOptions::QUERY => [
                    'access_key' => $this->apiKey,
                    'query'      => $text
                ]
            ]);
        } catch (GuzzleException $e) {
            throw LanguageDetectionException::httpError($e);
        }

        $json = (string) $response->getBody();
        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw LanguageDetectionException::invalidJsonData(json_last_error_msg());
        }

        if (! $data['success']) {
            throw LanguageDetectionException::apiError($data['error']);
        }

        $results = [];

        foreach ($data['results'] as $result) {
            $results[] = new LanguageDetectionResult($result);
        }

        return $results;
    }
}

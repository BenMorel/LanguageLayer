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
     * Detects the possible languages for the given text.
     *
     * Several results may be returned.
     *
     * @param string $text The text to detect.
     *
     * @return LanguageDetectionResult[]
     *
     * @throws LanguageDetectionException If the detection fails for any reason.
     */
    public function detectLanguages(string $text) : array
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

    /**
     * Attempts to detect a single language for the given text.
     *
     * If the API returns a single reliable result (even among other non-reliable results), the reliable result is returned.
     * If the API returns a single, non-reliable result, this result is returned unless `$forceReliable` is set to true.
     *
     * If no single acceptable result is found, an exception is thrown.
     *
     * @param string $text          The text to detect.
     * @param bool   $forceReliable If false (default), a single language, even if not reliable, is accepted.
     *                              If true, this method will only return a reliable result or throw an exception.
     *
     * @return string The 2-digit language code of the detected language.
     *
     * @throws LanguageDetectionException If the detection fails, or there is no single result.
     */
    public function detectLanguage(string $text, bool $forceReliable = false) : string
    {
        $results = $this->detectLanguages($text);

        /** @var LanguageDetectionResult[] $reliableResults */
        $reliableResults = [];

        foreach ($results as $result) {
            if ($result->isReliableResult()) {
                $reliableResults[] = $results;
            }
        }

        $resultCount = count($results);
        $reliableResultCount = count($reliableResults);

        if ($reliableResultCount === 1) {
            return $reliableResults[0]->getLanguageCode();
        }

        if (! $forceReliable && $resultCount === 1) {
            return $results[0]->getLanguageCode();
        }

        throw LanguageDetectionException::noSingleLanguage($resultCount, $reliableResultCount);
    }
}

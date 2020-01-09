<?php

declare(strict_types=1);

namespace BenMorel\LanguageLayer;

/**
 * An exception thrown if the language detection fails for any reason.
 *
 * The error message will always contain the reason for the failure.
 *
 * If the exception is due to an HTTP error, the GuzzleException can be inspected with getPrevious().
 * If the exception is due to an error reported by the API, it can be inspected with getCode() and getType().
 *
 * See the LanguageLayer documentation for error types and codes:
 * https://languagelayer.com/documentation
 */
class LanguageDetectionException extends \Exception
{
    /**
     * The error type, such as 'invalid_access_key'.
     *
     * If this exception is not due to an error reported by the API itself, this will be null.
     *
     * @var string|null
     */
    private $type;

    /**
     * Class constructor.
     *
     * @param string          $message
     * @param int             $code
     * @param string|null     $type
     * @param \Throwable|null $previous
     */
    private function __construct(string $message, int $code = 0, ?string $type = null, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->type = $type;
    }

    /**
     * @param \Exception $e
     *
     * @return LanguageDetectionException
     */
    public static function httpError(\Exception $e) : self
    {
        return new self('An error occurred while querying the LanguageLayer API.', 0, null, $e);
    }

    /**
     * @param string $jsonError
     *
     * @return LanguageDetectionException
     */
    public static function invalidJsonData(string $jsonError) : self
    {
        return new self('Invalid JSON data received from the LanguageLayer API: ' . $jsonError);
    }

    /**
     * @param array $error A raw error returned by the API, as an associative array.
     *
     * @return LanguageDetectionException
     */
    public static function apiError(array $error) : self
    {
        return new self(
            $error['info'],
            $error['code'],
            $error['type']
        );
    }

    /**
     * @param int $resultCount
     * @param int $reliableResultCount
     *
     * @return LanguageDetectionException
     */
    public static function noSingleLanguage(int $resultCount, int $reliableResultCount) : self
    {
        return new self(sprintf(
            'Could not reliably determine a single language for this text: %d possible language(s), %d reliable.',
            $resultCount,
            $reliableResultCount
        ));
    }

    /**
     * Returns the error type, such as 'invalid_access_key'.
     *
     * If this exception is not due to an error reported by the API itself, this method returns null.
     *
     * @return string|null
     */
    public function getType() : ?string
    {
        return $this->type;
    }
}

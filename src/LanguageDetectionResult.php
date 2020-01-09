<?php

declare(strict_types=1);

namespace BenMorel\LanguageLayer;

/**
 * A language detection result returned by the API.
 */
class LanguageDetectionResult
{
    /**
     * @var string
     */
    private $languageCode;

    /**
     * @var string
     */
    private $languageName;

    /**
     * @var float
     */
    private $probability;

    /**
     * @var float
     */
    private $percentage;

    /**
     * @var bool
     */
    private $reliableResult;

    /**
     * Class constructor.
     *
     * @param array $result A raw result returned by the API, as an associative array.
     */
    public function __construct(array $result)
    {
        $this->languageCode   = $result['language_code'];
        $this->languageName   = $result['language_name'];
        $this->probability    = $result['probability'];
        $this->percentage     = $result['percentage'];
        $this->reliableResult = $result['reliable_result'];
    }

    /**
     * Returns the 2-digit language code of the detected language.
     *
     * @return string
     */
    public function getLanguageCode() : string
    {
        return $this->languageCode;
    }

    /**
     * Returns the name of the detected language.
     *
     * @return string
     */
    public function getLanguageName() : string
    {
        return $this->languageName;
    }

    /**
     * Returns the probability of the detection.
     *
     * This is a numerical value based on the length of the provided query text
     * and how well it is identified as a language. For reference only.
     *
     * @return float
     */
    public function getProbability() : float
    {
        return $this->probability;
    }

    /**
     * Returns the confidence percentage of the detection.
     *
     * This is a percentage value (0-100%) that puts in perspective the API's confidence about the
     * respective language match and presents the confidence margin between multiple matches.
     *
     * @return float
     */
    public function getPercentage() : float
    {
        return $this->percentage;
    }

    /**
     * Returns whether or not the API is completely confident about the main match.
     *
     * @return bool
     */
    public function isReliableResult() : bool
    {
        return $this->reliableResult;
    }
}

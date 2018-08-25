# LanguageLayer API client for PHP

Perform language detection in PHP using the [LanguageLayer](https://languagelayer.com/) API.

[![Latest Stable Version](https://poser.pugx.org/benmorel/languagelayer/v/stable)](https://packagist.org/packages/benmorel/languagelayer)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](http://opensource.org/licenses/MIT)

## Installation

This library is installable via [Composer](https://getcomposer.org/):

```bash
composer require benmorel/languagelayer
```

## Requirements

This library requires PHP 7.1 or later.

## Quickstart

You need a free API key from LanguageLayer to get started.

Just instantiate the LanguageLayer client, and start detecting:

```php
use BenMorel\LanguageLayer\LanguageLayerClient;

$client = new LanguageLayerClient('YOUR API KEY');

$results = $client->detectLanguages('Some text. Try more than a few words for accurate detection.');

foreach ($results as $result) {
    if ($result->isReliableResult()) {
        echo $result->getLanguageCode();
    }
}
```

The `detectLanguages()` method returns an array of [LanguageDetectionResult](https://github.com/BenMorel/LanguageLayer/blob/0.1.0/src/LanguageDetectionResult.php) objects,
that you can inspect to decide what to do with each detected language.

### Detecting a single language

As a convenience, a `detectLanguage()` methods helps you detect a single language from a text:

```php
$languageCode = $client->detectLanguage('Some text. Try more than a few words for accurate detection.');
```

This method loops through the results to find a single *reliable* result. If it there is no reliable result, but the API
returned a single result, it will also accept it, unless the second parameter, `$forceReliable`, is set to true:

```
$languageCode = $client->detectLanguage('...', true); // will not accept a single result, if not "reliable"
```

If no single, acceptable result is found, a `LanguageDetectionException` is thrown.

### Error handling

Any kind of error—an HTTP error, an error returned by the API, or any other kind of error related to this
library—throws a `LanguageDetectionException`.

Therefore you should wrap all your `detectLanguage()` and `detectLanguages()` calls in a `try`/`catch` block:

```php
use BenMorel\LanguageLayer\LanguageDetectionException;

// …

try {
    $languageCode = $client->detectLanguage('...');
catch (LanguageDetectionException $exception) {
    // deal with it.
}
```

If the exception was caused by an HTTP error, you can inspect the underlying `GuzzleException`
by calling `$exception->getPrevious()` if needed.

If the exception was caused by an error returned by the LanguageLayer API itself, you can inspect it,
in addition to the exception message, with `$exception->getCode()` and `$exception->getType()`.

You can, for example, act upon specific API errors:

```php
try {
    $languageCode = $client->detectLanguage('...');
} catch (LanguageDetectionException $exception) {
    switch ($exception->getType()) {
        case 'invalid_access_key':
        case 'usage_limit_reached':
            // report the error!
            break;

        case 'rate_limit_reached':
            // slow down!
        
        // ...
    }
}
```

Note: if the exception was not caused by an error returned by the API itself, `getType()` will return `null`.

See the [LanguageLayer documentation](https://languagelayer.com/documentation#error_codes) for a list of error codes and types.

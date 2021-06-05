<?php
/**
 * Website: http://sourceforge.net/projects/simplehtmldom/
 * Acknowledge: Jose Solorzano (https://sourceforge.net/projects/php-html/)
 *
 * Licensed under The MIT License
 * See the LICENSE file in the project root for more information.
 *
 * Authors:
 *   S.C. Chen
 *   John Schlick
 *   Rus Carroll
 *   logmanoriginal
 *
 * Contributors:
 *   Yousuke Kumakura
 *   Vadim Voituk
 *   Antcs
 *   Igor (Dicr) Tarasov
 *
 * Version $Rev$
 */

namespace simplehtmldom;

use function curl_close;
use function curl_exec;
use function curl_getinfo;
use function curl_init;
use function curl_setopt;
use function error_log;
use function explode;
use function extension_loaded;
use function file_get_contents;
use function filter_var;
use function ini_get;
use function parse_url;
use function preg_match;
use function stream_context_create;
use function strlen;
use function strtolower;

use const CURLINFO_RESPONSE_CODE;
use const CURLOPT_BUFFERSIZE;
use const CURLOPT_FOLLOWLOCATION;
use const CURLOPT_HTTPHEADER;
use const CURLOPT_RETURNTRANSFER;
use const CURLOPT_URL;
use const FILTER_VALIDATE_URL;
use const PHP_URL_SCHEME;

require_once __DIR__ . '/HtmlDocument.php';

/**
 * Class HtmlWeb
 */
class HtmlWeb
{
    /**
     * @param string $url
     * @return HtmlDocument|null Returns the DOM for a webpage
     * Returns null if the cURL extension is not loaded and allow_url_fopen=Off
     * Returns null if the provided URL is invalid (not PHP_URL_SCHEME)
     * Returns null if the provided URL does not specify the HTTP or HTTPS protocol
     * @noinspection PhpMethodMayBeStaticInspection
     */
    public function load($url)
    {
        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        $scheme = parse_url($url, PHP_URL_SCHEME);
        if ($scheme) {
            switch (strtolower($scheme)) {
                case 'http':
                case 'https':
                    break;

                default:
                    return null;
            }

            if (extension_loaded('curl')) {
                return self::load_curl($url);
            }

            if (ini_get('allow_url_fopen')) {
                return self::load_fopen($url);
            }

            /** @noinspection ForgottenDebugOutputInspection */
            error_log(__FUNCTION__ . ' requires either the cURL extension or allow_url_fopen=On in php.ini');
        }

        return null;
    }

    /**
     * cURL implementation of load
     *
     * @param string
     * @return HtmlDocument|null
     * @noinspection PhpComposerExtensionStubsInspection
     */
    private static function load_curl($url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        // There is no guarantee this request will be fulfilled
        // -- https://www.php.net/manual/en/function.curl-setopt.php
        curl_setopt($ch, CURLOPT_BUFFERSIZE, MAX_FILE_SIZE);

        // There is no guarantee this request will be fulfilled
        $header = [
            'Accept: text/html', // Prefer HTML format
            'Accept-Charset: utf-8', // Prefer UTF-8 encoding
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        $doc = curl_exec($ch);

        if (curl_getinfo($ch, CURLINFO_RESPONSE_CODE) !== 200) {
            return null;
        }

        curl_close($ch);

        if (strlen($doc) > MAX_FILE_SIZE) {
            return null;
        }

        return new HtmlDocument($doc);
    }

    /**
     * fopen implementation of load
     *
     * @param string $url
     */
    private static function load_fopen($url)
    {
        // There is no guarantee this request will be fulfilled
        $context = stream_context_create(['http' => [
            'header' => [
                'Accept: text/html', // Prefer HTML format
                'Accept-Charset: utf-8', // Prefer UTF-8 encoding
            ],
            'ignore_errors' => true // Always fetch content
        ]]);

        $doc = file_get_contents($url, false, $context, 0, MAX_FILE_SIZE + 1);

        if (isset($http_response_header)) {
            foreach ($http_response_header as $rh) {
                // https://stackoverflow.com/a/1442526
                $parts = (array)explode(' ', $rh, 3);

                if (preg_match('/HTTP\/\d\.\d/', $parts[0])) {
                    $code = $parts[1];
                }
            } // Last code is final status

            if (! isset($code) || $code !== '200') {
                return null;
            }
        }

        if (strlen($doc) > MAX_FILE_SIZE) {
            return null;
        }

        return new HtmlDocument($doc);
    }
}

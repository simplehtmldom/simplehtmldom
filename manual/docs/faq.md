# FAQ

## Problem with finding

Q: Element not found in such case: `$html->find('div[style=padding: 0px 2px;] span[class=rf]');`

A: If there is blank in selectors, quote it!
$html->find('div[style="padding: 0px 2px;"] span[class=rf]');

## Problem with hosting

Q: On my local server everything works fine, but when I put it on my esternal server it doesn't work.

A: The "file_get_dom" function is a wrapper of "file_get_contents" function,  you must set "allow_url_fopen" as TRUE in "php.ini" to allow accessing files via HTTP or FTP. However, some hosting venders disabled PHP's "allow_url_fopen" flag for security issues... PHP provides excellent support for "curl" library to do the same job, Use curl to get the page, then call "str_get_dom" to create DOM object.

Example:

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, 'http://????????');
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
$str = curl_exec($curl);
curl_close($curl);

$html= str_get_html($str);
...

## Behind a proxy

Q: My server is behind a Proxy and i can't use file_get_contents b/c it returns a unauthorized error.

A: Thanks for Shaggy to provide the solution:

// Define a context for HTTP.
$context = array
(
       'http' => array
       (
              'proxy' => 'addresseproxy:portproxy', // This needs to be the server and the port of the NTLM Authentication Proxy Server.
              'request_fulluri' => true,
       ),
);

$context = stream_context_create($context);

$html= file_get_html('http://www.php.net', false, $context);
...

## Memory leak

Q: This script is leaking memory seriously... After it finished running, it's not cleaning up dom object properly from memory..

A: Due to php5 circular references memory leak, after creating DOM object, you must call $dom->clear() to free memory if call file_get_dom() more then once.

Example:

$html = file_get_html(...);
// do something...
$html->clear();
unset($html);
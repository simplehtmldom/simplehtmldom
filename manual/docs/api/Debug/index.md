---
title: Debug
---

Provides the ability to see debug messages for deprecated functions, malformed documents and parsing issues. Debug messages can be send to a custom message handler or written to error_log (default).

## Example

```php
<?php
include_once 'HtmlDocument.php';
include_once 'Debug.php';
use simplehtmldom\HtmlDocument;
use simplehtmldom\Debug;

Debug::enable();

$dom = new HtmlDocument();
$dom->load('<html></html>');
$dom->root->children(); // This causes a deprecation warning

Debug::disable();
```

**Output**

```
[DEBUG] [/var/www/html/simplehtmldom/Debug.php:30] [/var/www/html/simplehtmldom/test.php:7] "Debug mode has been enabled"
[DEBUG] [/var/www/html/simplehtmldom/HtmlNode.php:83] [/var/www/html/simplehtmldom/test.php:11] "simplehtmldom\HtmlNode->children() has been deprecated and will be removed in the next major version of simplehtmldom. Use simplehtmldom\HtmlNode->childNodes() instead."
[DEBUG] [/var/www/html/simplehtmldom/Debug.php:38] [/var/www/html/simplehtmldom/test.php:13] "Debug mode has been disabled"
```
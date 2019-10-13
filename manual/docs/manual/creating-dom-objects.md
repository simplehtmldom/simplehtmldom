---
title: Creating HTML DOM objects
---

## From string

```php
<?php
include_once 'HtmlDocument';
use simplehtmldom\HtmlDocument;

$html = new HtmlDocument();
$html->load('<html><body>Hello!</body></html>');
```

## From URL

```php
<?php
include_once 'HtmlWeb';
use simplehtmldom\HtmlWeb;

$html = new HtmlWeb();
$html->load('http://www.google.com/');

```

## From file

```php
<?php
include_once 'HtmlDocument';
use simplehtmldom\HtmlDocument;

$html = new HtmlDocument();
$html->loadFile('test.htm');
```
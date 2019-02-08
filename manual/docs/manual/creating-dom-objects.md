---
title: Creating HTML DOM objects
---

## Functional

```php
// Create a DOM object from a string
$html = str_get_html('<html><body>Hello!</body></html>');

// Create a DOM object from a URL
$html = file_get_html('http://www.google.com/');

// Create a DOM object from a HTML file
$html = file_get_html('test.htm');
```

## Object Oriented

```php
// Create a DOM object
$html = new simple_html_dom();

// Load HTML from a string
$html->load('<html><body>Hello!</body></html>');

// Load HTML from a URL
$html->load_file('http://www.google.com/');

// Load HTML from a HTML file
$html->load_file('test.htm');
```
# Quick Start

## Read contents from an HTML document

This example demonstrates how to read text contents from an HTML document in a single line of code.

```php
echo file_get_html('https://www.google.com/')->plaintext;
```

## Read data from an HTML document

This example demonstrates how to parse image urls and anchor locations from an HTML document.

```php
// Load HTLM document from URL
$html = file_get_html('https://www.google.com/');

// Find all images and return their URLs
foreach($html->find('img') as $element)
    echo $element->src . '<br>';

// Find all anchors and return their tagets
foreach($html->find('a') as $element)
    echo $element->href . '<br>';
```

## Modify HTML elements

This example demonstrates how to modify existing HTML and get the modified HTML back.

```php
$html = '<div id="hello">Hello,</div><div id="world">world!</div>'

// Load HTML document from string
$html = str_get_html($html);

// Add class "bar" to the second div element
$html->find('div', 1)->class = 'bar';

// Set text to "foo" for the div element with id "hello"
$html->find('div[id=hello]', 0)->innertext = 'foo';

echo $html;

// Returns
//
// <div id="hello">foo</div><div id="world" class="bar">world!</div>
```

## Collect information from Slashdot

This example demonstrates how to collect information from [Slashdot](https://slashdot.org/) for further processing.

```php
// Load HTML document from URL
$html = file_get_html('https://slashdot.org/');

// Find stories
$articles = $html->find('article[data-fhtype="story"]');

// Collect information
foreach($articles as $article) {
    $item['title'] = $article->find('.story-title', 0)->plaintext;
    $item['intro'] = $article->find('.p', 0)->plaintext;
    $item['details'] = $article->find('.details', 0)->plaintext;
    $items[] = $item;
}

print_r($items);
```
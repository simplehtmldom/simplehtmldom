# Quick Start

Find below sample code that demonstrate the fundamental features of PHP Simple
HTML DOM Parser.

## Read plain text from HTML document

```php
echo file_get_html('https://www.google.com/')->plaintext;
```

Loads the specified HTML **document** into memory, parses it and returns the
plain text. Note that [`file_get_html`](api/api.md) supports local files as well
as remote files!

## Read plaint text from HTML string

```php
echo str_get_html('<ul><li>Hello, World!</li></ul>')->plaintext;
```

Parses the provided HTML **string** and returns the plain text. Note that the
parser handles partial documents as well as full documents.

## Read specific elements from HTML document

```php
$html = file_get_html('https://www.google.com/');

foreach($html->find('img') as $element)
    echo $element->src . '<br>';

foreach($html->find('a') as $element)
    echo $element->href . '<br>';
```

Loads the specified document into memory and returns a list of image sources as
well as anchor links. Note that [`find`](manual/finding-html-elements.md)
supports [CSS](https://www.w3.org/TR/selectors/) selectors to find elements in
the DOM.

## Modify HTML documents

```php
$doc = '<div id="hello">Hello, </div><div id="world">World!</div>';

$html = str_get_html($doc);

$html->find('div', 1)->class = 'bar';
$html->find('div[id=hello]', 0)->innertext = 'foo';

echo $html; // <div id="hello">foo</div><div id="world" class="bar">World!</div>
```

Parses the provided HTML string and replaces elements in the DOM before returning
the updated HTML string. In this example, the class for the second `div` element
is set to `bar` and the inner text for the first `div` element to `foo`.

Note that [`find`](manual/finding-html-elements.md) supports a second parameter
to return a single element from the array of matches.

Note that attributes can be accessed directly by the means of magic methods
(`->class` and `->innertext` in the example above).

## Collect information from Slashdot

```php
$html = file_get_html('https://slashdot.org/');

$articles = $html->find('article[data-fhtype="story"]');

foreach($articles as $article) {
    $item['title'] = $article->find('.story-title', 0)->plaintext;
    $item['intro'] = $article->find('.p', 0)->plaintext;
    $item['details'] = $article->find('.details', 0)->plaintext;
    $items[] = $item;
}

print_r($items);
```

Collects information from [Slashdot](https://slashdot.org/) for further processing.

Note that the combination of CSS selectors and magic methods make the process of
parsing HTML documents a simple task that is easy to understand.
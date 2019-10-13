---
title: Finding HTML Elements
---

## By tag name

```php
// Find all anchors, returns a array of element objects
$ret = $html->find('a');

// Find all anchors and images, returns an array of element objects
$ret = $html->find('a, img');

// Find (N)th anchor, returns element object or null if not found (zero based)
$ret = $html->find('a', 0);

// Find last anchor, returns element object or null if not found (zero based)
$ret = $html->find('a', -1);
```

## By class name or id

```php
// Find all element which id=foo
$ret = $html->find('#foo');

// Find all element which class=foo
$ret = $html->find('.foo');
```

## By attribute

```php
// Find all <div> with the id attribute
$ret = $html->find('div[id]');

// Find all <div> which attribute id=foo
$ret = $html->find('div[id=foo]');

// Find all anchors and images with the "title" attribute
$ret = $html->find('a[title], img[title]');

// Find all element has attribute id
$ret = $html->find('*[id]');
```

## Descendants

```php
// Find all <li> in <ul>
$es = $html->find('ul li');

// Find Nested <div> tags
$es = $html->find('div div div');

// Find all <td> in <table> which class=hello
$es = $html->find('table.hello td');

// Find all td tags with attribite align=center in table tags
$es = $html->find('table td[align=center]');
```

## Nested elements

```php
// Find all <li> in <ul>
foreach($html->find('ul') as $ul)
{
       foreach($ul->find('li') as $li)
       {
             // do something...
       }
}

// Find first <li> in first <ul>
$e = $html->find('ul', 0)->find('li', 0);
```

## Text, comments and CDATA

```php
// Find all text blocks
$es = $html->find('text');

// Find all comment (<!--...-->) blocks
$es = $html->find('comment');

// Find CDATA blocks
$es = $html->find('cdata');
```
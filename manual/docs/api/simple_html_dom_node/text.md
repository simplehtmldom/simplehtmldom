# text

```php
text ( [ bool $trim = true ] ) : string
```

Returns the text representation of the current node and its decendants.

| Parameter     | Description
| ---------     | -----------
| `trim`        | Removes any whitespace around the text if set to `true`.

**Remarks**

* You can use the short-hand version [`$node->plaintext`](__get.md) instead of
`$node->text()` which also works on the document level (automatically selects
the root element of the document).

**Examples**

```php

// With trimming
$node->text();
$node->plaintext;
$html->plaintext;

// Withoug trimming
$node->text(false);
```
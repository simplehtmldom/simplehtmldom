```php
text ( [ bool $trim = true ] ) : string
```

| Parameter | Description                                    |
|-----------|------------------------------------------------|
| `trim`    | Removes whitespace around the text if enabled. |

Returns the text representation of the current node and its descendants.

**Remarks**

* You can use the shorthand version [`$node->plaintext`](__get.md) instead of
`$node->text()` which also works on the document level (automatically selects
the root element of the document).

**Examples**

```php
$node->text();
$node->plaintext;
$html->plaintext;
```
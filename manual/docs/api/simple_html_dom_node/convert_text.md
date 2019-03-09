# convert_text

```php
convert_text ( string $text ) : string
```

| Parameter | Description
| --------- | -----------
| `text`    | Text to convert.

Assumes that the provided text is in the form of the configured source character set (see [`sourceCharset`](../simple_html_dom_node/) and converts it to the specified target character set (see [`targetCharset`](../simple_html_dom_node/)).

Returns the converted text.
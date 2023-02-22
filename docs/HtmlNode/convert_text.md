```php
convert_text ( string $text ) : string
```

| Parameter | Description      |
|-----------|------------------|
| `text`    | Text to convert. |

Assumes that the provided text is in the form of the configured source character set (see [`sourceCharset`](../HtmlDocument/index.md)) and converts it to the specified target character set (see [`targetCharset`](../HtmlDocument/index.md)).

Returns the converted text.
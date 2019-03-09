# prepare (protected)

```php
prepare ( string $str [, bool $lowercase = true [, string $defaultBRText = DEFAULT_BR_TEXT [, string $defaultSpanText = DEFAULT_SPAN_TEXT ]]] )
```

Initializes the DOM object.

| Parameters        | Description
| ----------        | -----------
| `str`             | The HTML document string.
| `lowercase`       | Tag names are parsed in lowercase letters if enabled.
| `defaultBRText`   | Defines the default text to return for `<br>` elements.
| `defaultSpanText` | Defines the default text to return for `<span>` elements.
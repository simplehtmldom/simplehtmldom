```php
__construct ( [ string $str = null [, bool $lowercase = true [, bool $forceTagsClosed = true [, string $target_charset = DEFAULT_TARGET_CHARSET [, bool $stripRN = true [, string $defaultBRText = DEFAULT_BR_TEXT [, string $defaultSpanText = DEFAULT_SPAN_TEXT [, int $options = 0 ]]]]]]]]) : object
```

Creates a new `HtmlDocument` object.

| Parameter         | Description                                                                                                                        |
|-------------------|------------------------------------------------------------------------------------------------------------------------------------|
| `str`             | The HTML document string.                                                                                                          |
| `lowercase`       | Tag names are parsed in lowercase letters if enabled.                                                                              |
| `forceTagsClosed` | Tags inside block tags are forcefully closed if the closing tag was omitted.                                                       |
| `target_charset`  | Defines the target charset for text returned by the parser.                                                                        |
| `stripRN`         | Newline characters are replaced by whitespace if enabled.                                                                          |
| `defaultBRText`   | Defines the default text to return for `<br>` elements.                                                                            |
| `defaultSpanText` | Defines the default text to return for `<span>` elements.                                                                          |
| `options`         | Additional options for the parser. Currently supports `'HDOM_SMARTY_AS_TEXT'` to remove [Smarty](https://www.smarty.net/) scripts. |

Returns the object.
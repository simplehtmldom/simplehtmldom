---
title: str_get_html
---

# str_get_html

```php
str_get_html ( string $str [, bool $lowercase = true [, bool $forceTagsClosed = true [, string $target_charset = DEFAULT_TARGET_CHARSET [, bool $stripRN = true [, string $defaultBRText = DEFAULT_BR_TEXT [, string $defaultSpanText = DEFAULT_SPAN_TEXT ]]]]]] )
```

Parses the provided string and returns the DOM object.

| Parameter         | Description
| ---------         | -----------
| `str`             | The HTML document string.
| `lowercase`       | Forces lowercase matching of tags if enabled. This is very useful when loading documents with mixed naming conventions.
| `forceTagsClosed` | Obsolete. This parameter is no longer used by the parser.
| `target_charset`  | Defines the target charset when returning text from the document.
| `stripRN`         | If enabled, removes newlines before parsing the document.
| `defaultBRText`   | Defines the default text to return for `<br>` elements.
| `defaultSpanText` | Defines the default text to return for `<span>` elements.

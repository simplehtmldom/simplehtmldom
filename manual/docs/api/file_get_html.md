---
title: file_get_html
---

# file_get_html

```php
file_get_html ( string $url [, bool $use_include_path = false [, resouce $context = null [, int $offset = 0 [, int $maxLen = -1 [, bool $lowercase = true [, bool $forceTagsClosed = true [, string $target_charset = DEFAULT_TARGET_CHARSET [, bool $stripRN = true [, string $defaultBRText = DEFAULT_BR_TEXT [, string $defaultSpanText = DEFAULT_SPAN_TEXT ]]]]]]]]]] )
```

Parses the provided file and returns the DOM object.

| Parameter             | Description
| ---------             | -----------
| `url`                 | Name or URL of the file to read.
| `use_include_path`    | See [`file_get_contents`](http://php.net/manual/en/function.file-get-contents.php#refsect1-function.file-get-contents-parameters)
| `context`             | See [`file_get_contents`](http://php.net/manual/en/function.file-get-contents.php#refsect1-function.file-get-contents-parameters)
| `offset`              | See [`file_get_contents`](http://php.net/manual/en/function.file-get-contents.php#refsect1-function.file-get-contents-parameters)
| `maxLen`              | See [`file_get_contents`](http://php.net/manual/en/function.file-get-contents.php#refsect1-function.file-get-contents-parameters)
| `lowercase`           | Forces lowercase matching of tags if enabled. This is very useful when loading documents with mixed naming conventions.
| `forceTagsClosed`     | Obsolete. This parameter is no longer used by the parser.
| `target_charset`      | Defines the target charset when returning text from the document.
| `stripRN`             | If enabled, removes newlines before parsing the document.
| `defaultBRText`       | Defines the default text to return for `<br>` elements.
| `defaultSpanText`     | Defines the default text to return for `<span>` elements.

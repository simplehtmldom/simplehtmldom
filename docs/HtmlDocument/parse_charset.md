```php
parse_charset ()
```

Parses the charset.

If the callback function `get_last_retrieve_url_contents_content_type` exists, it is assumed to return the content type header for the current document as string.

Uses the charset from the metadata of the page if defined.

If none of the previous conditions are met, the charset is determined by `mb_detect_encoding` if multibyte support is active.

If multibyte support is not active the charset is assumed to be `'UTF-8'`.
```php
copy_until ( string $chars ) : string
```

Copies all characters starting at the current parsing position in the document. Sets the parsing position to the first character that matches any of the characters in the provided list of characters.

| Parameter | Description                              |
|-----------|------------------------------------------|
| `chars`   | A list of characters to stop copying at. |

Returns the copied characters.
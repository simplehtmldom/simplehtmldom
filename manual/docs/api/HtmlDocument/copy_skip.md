---
title: copy_skip (protected)
---

```php
copy_skip ( string $chars ) : string
```

Skips characters starting at the current parsing position in the document. Sets the parsing position to the first character not in the provided list of characters.

| Parameter | Description
| --------- | -----------
| `chars`   | A list of characters to skip.

Returns the skipped characters.
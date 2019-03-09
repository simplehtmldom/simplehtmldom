# restore_noise (protected)

```php
restore_noise ( string $text ) : string
```

Restores noise in the provided string by replacing noise placeholders by their original contents.

| Parameter | Description
| --------- | -----------
| `text`    | A string (potentially) containing noise placeholders.

Returns the string with original contents restored or the original string if it doesn't contain noise placeholders.
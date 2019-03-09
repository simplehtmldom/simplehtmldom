# find

```php
find ( string $selector [, int $idx = null [, bool $lowercase = false ]] ) : mixed
```

Finds elements in the DOM.

| Parameter     | Description
| ---------     | -----------
| `selector`    | A [CSS style selector](/manual/selectors).
| `idx`         | Index of the element to return.
| `lowercase`   | Matches tag names case insensitive when enabled.

Returns an array of matches or a single element if `idx` is defined.
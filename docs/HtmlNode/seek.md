```php
seek (
    string $selector
    , array &$ret
    , string $parent_cmd
    [, bool $lowercase = false ]
)
```

| Parameter    | Description                                                |
|--------------|------------------------------------------------------------|
| `selector`   | The current selector.                                      |
| `ret`        | Previous return value (starting point).                    |
| `parent_cmd` | The combinator used before the current selector.           |
| `lowercase`  | Matches tag names case insensitive (lowercase) if enabled. |

Starts by searching for child elements of `$ret` that match the specified selector. Adds matching elements to `$ret` (for the next iteration).
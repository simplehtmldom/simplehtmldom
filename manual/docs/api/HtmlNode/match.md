---
title: match (protected)
---

```php
match (
    string $exp
    , string $pattern
    , string $value
    , string $case_sensitivity
) : bool
```

| Parameter             | Description
| ---------             | -----------
| `exp`                 | Expression
| `pattern`             | Pattern
| `value`               | Value
| `case_sensitivity`    | Case sensitivity

Matches a single attribute value against the specified attribute selector. See also [`find`](../find/).
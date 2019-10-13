---
title: remove_noise (protected)
---

```php
remove_noise ( string $pattern [, bool $remove_tag = false] )
```

Replaces noise in the document (i.e. scripts) by placeholders and adds the removed contents to `$this->noise`.

_Note_: Noise is replaced by placeholders in order to allow restoring the original contents. Placeholders take the form of `'___noise___1000'` where the number is increased by one for each removed noise.

| Parameter     | Description
| ---------     | -----------
| `pattern`     | A regular expression that matches the noise to remove.
| `remove_tag`  | Removes the entire match when enabled or submatches when disabled.
---
title: save
---

```php
save ( [ string $filepath = '' ] ) : string
```

Writes the current node to file.

| Parameter     | Description
| ---------     | -----------
| `filepath`    | Writes to file if the provided file path is not empty.

Returns the document string.

**Examples**

```php
$string = $node->save();
$string = $node->save($file);
```
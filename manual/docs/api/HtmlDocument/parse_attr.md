---
title: parse_attr (protected)
---

```php
parse_attr ( object $node, string $name, array &$space )
```

Parses a single attribute starting at the current parsing position in the document.

| Parameter | Description
| --------- | -----------
| `node`    | The current element (node).
| `name`    | The attribute name.
| `space`   | An array of whitespace sorounding the current attribute (see [Attribute Whitespace](../definitions/#attribute-whitespace)).
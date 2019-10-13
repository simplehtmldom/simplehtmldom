---
title: addClass
---

```php
addClass ( mixed $class )
```

| Parameter | Description
| --------- | -----------
| `class`   | Specifies one or more class names to be added.

Adds one or more class names to the current node.

**Remarks**

* To add more than one class, separate the class names with space or provide them as an array.

**Examples**

```php
$node->addClass('hidden');
$node->addClass('article important');
$node->addClass(array('article', 'new'));
```
---
title: __isset
---

```php
__isset ( string $name ) : bool
```

| Parameter | Description
| --------- | -----------
| `name`    | `outertext`, `innertext`, `plaintext` or attribute name.

See [magic methods](http://php.net/manual/en/language.oop5.overloading.php#object.get)

Returns true if the provided name is a valid attribute name or any of the values in the table below. False otherwise.

| Name              | Description
| ----              | -----------
| `outertext`       | Returns the outer text of the current node.
| `innertext`       | Returns the inner text of the current node.
| `plaintext`       | Returns the plain text of the current node.
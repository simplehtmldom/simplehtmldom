```php
__set ( string $name, mixed $value )
```

| Parameter | Description                                 |
|-----------|---------------------------------------------|
| `name`    | `outertext`, `innertext` or attribute name. |
| `value`   | Value to set.                               |

See [magic methods](http://php.net/manual/en/language.oop5.overloading.php#object.get)

Sets the outer text of the current node to `$value` if `$name` is `outertext`.

Sets the inner text of the current node to `$value` if `$name` is `innertext`.

Otherwise, adds or updates an attribute with name `$name` and value `$value` to the current node.
# __get

```php
__get ( string $name ) : mixed
```

| Parameter | Description
| --------- | -----------
| `name`    | `outertext`, `innertext`, `plaintext`, `xmltext` or attribute name.

See [magic methods](http://php.net/manual/en/language.oop5.overloading.php#object.get)

If the provided name is a valid attribute name, returns the attribute value. Otherwise a value according to the table below.

| Name              | Description
| ----              | -----------
| `outertext`       | Returns the outer text of the current node.
| `innertext`       | Returns the inner text of the current node.
| `plaintext`       | Returns the plain text of the current node.
| `xmltext`         | Returns the xml representation for the inner text of the current node as a CDATA section.

Returns nothing if the provided name is neither a valid attribute name, nor a valid parameter name.
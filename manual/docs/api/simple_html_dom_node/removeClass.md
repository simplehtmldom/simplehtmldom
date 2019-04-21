# removeClass

```php
removeClass ( [ mixed $class = null ] )
```

| Parameter | Description
| --------- | -----------
| `class`   | Specifies one or more class names to be removed.

Removes one or more class names from the current node.

**Remarks**

* To remove more than one class, separate the class names with space or provide them as an array.
* If no parameter is specified, this method will remove all class names from the current node.

**Examples**

```php
$node->removeClass('hidden');
$node->removeClass('article important');
$node->removeClass(array('article', 'new'));
$node->removeClass();
```
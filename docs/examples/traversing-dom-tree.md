If you are not so familiar with HTML DOM, check this [link](http://php.net/manual/en/book.dom.php) to learn more...

```php
// Example
echo $html->find("#div1", 0)->children(1)->children(1)->children(2)->id;
// or
echo $html->getElementById("div1")->childNodes(1)->childNodes(1)->childNodes(2)->getAttribute('id');
```

You can also call methods using the **Camel naming conventions**.

| Method                                 | Description                                                                                      |
|----------------------------------------|--------------------------------------------------------------------------------------------------|
| `$e->children( [int $index] ) : mixed` | Returns the Nth **child object** if **index** is set, otherwise return an **array of children**. |
| `$e->parent() : element`               | Returns the **parent** of element.                                                               |
| `$e->first_child() : element`          | Returns the **first child** of element, or **null** if not found.                                |
| `$e->last_child() : element`           | Returns the **last child** of element, or **null** if not found.                                 |
| `$e->next_sibling() : element`         | Returns the **next sibling** of element, or **null** if not found.                               |
| `$e->prev_sibling() : element`         | Returns the **previous sibling** of element, or **null** if not found.                           |
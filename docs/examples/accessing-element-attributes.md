# Get, Set and Remove attributes

```php
// Get attribute ( If the attribute is non-value attribute (eg. checked, selected...), it will returns true or false)
$value = $e->href;

// Set attribute(If the attribute is non-value attribute (eg. checked, selected...), set it's value as true or false)
$e->href = 'my link';

// Remove attribute, set it's value as null!
$e->href = null;

// Determine whether an attribute exist?
if(isset($e->href))
    echo 'href exist!';
```

# Magic attributes

```php
// Example
$html->load("<div>foo <b>bar</b></div>");
$e = $html->find("div", 0);

echo $e->tag; // Returns: " div"
echo $e->outertext; // Returns: " <div>foo <b>bar</b></div>"
echo $e->innertext; // Returns: " foo <b>bar</b>"
echo $e->plaintext; // Returns: " foo bar"
```

| Attribute name  | Description                                       |
|-----------------|---------------------------------------------------|
| `$e->tag`       | Read or write the **tag name** of element.        |
| `$e->outertext` | Read or write the **outer HTML text** of element. |
| `$e->innertext` | Read or write the **inner HTML text** of element. |
| `$e->plaintext` | Read or write the **plain text** of element.      |
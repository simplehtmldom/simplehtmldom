---
title: Customizing Parsing behavior
---

```php
// Write a function with parameter "$element"
function my_callback($element) {
        // Hide all <b> tags
        if ($element->tag=='b')
                $element->outertext = '';
}

// Register the callback function with it's function name
$html->set_callback('my_callback');

// Callback function will be invoked while dumping
echo $html;
```
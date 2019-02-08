---
title: Saving DOM objects
---

# Functional

```php
// Dumps the internal DOM tree back into string
$str = $html;

// Print it!
echo $html;
```

# Object Oriented

```php
// Dumps the internal DOM tree back into string
$str = $html->save();

// Dumps the internal DOM tree back into a file
$html->save('result.htm');
```
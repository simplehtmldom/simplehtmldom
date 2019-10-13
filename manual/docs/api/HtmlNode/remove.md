---
title: remove
---

```php
remove ( )
```

Removes the current node recursively from the DOM.
Does nothing if the node has no parent (root node);

**Example**

```php
$html->load(<<<EOD
<html>
<body>
<table>
	<tr><th>Title</th></tr>
	<tr><td>Row 1</td></tr>
</table>
</body>
</html>
EOD
);

$table = $html->find('table', 0);
$table->remove();

echo $html;

/**
 * Returns
 *
 * <html> <body>  </body> </html>
 */
```

**Remarks**

* Whitespace immediately **before** the removed node will remain in the DOM.
---
title: parent
---

```php
parent ( [ object $parent = null ] ) : object
```

| Parameter | Description
| --------- | -----------
| `parent`  | The parent node

* Returns the parent node of the current node if `$parent` is null.
* Sets the parent node of the current node if `$parent` is not null. In this case the current node is automatically added to the list of nodes in the parent node.
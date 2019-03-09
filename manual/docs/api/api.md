---
title: API Reference
---

# Parsing documents

The parser accepts documents in the form of URLs, files and strings. The document
must be accessible for reading and cannot exceed [`MAX_FILE_SIZE`](constants.md#max_file_size).

Name | Description
---- | -----------
`str_get_html( string $content ) : object`      | Creates a DOM object from string.
`file_get_html( string $filename ) : object`    | Creates a DOM object from file or URL.

# DOM methods & properties

Name | Description
---- | -----------
`__construct( [string $filename] ) : void` | Constructor, set the filename parameter will automatically load the contents, either text or file/url.
`plaintext : string` | Returns the contents extracted from HTML.
`clear() : void` | Clean up memory.
`load( string $content ) : void` | Load contents from string.
`save( [string $filename] ) : string` | Dumps the internal DOM tree back into a string. If the $filename is set, result string will save to file.
`load_file( string $filename ) : void` | Load contents from a file or a URL.
`set_callback( string $function_name ) : void` | Set a callback function.
`find( string $selector [, int $index] ) : mixed` | Find elements by the CSS selector. Returns the Nth element object if index is set, otherwise return an array of object.

# Element methods & properties

Name | Description
---- | -----------
`[attribute] : string` | Read or write element's attribute value.
`tag : string` | Read or write the tag name of element.
`outertext : string` | Read or write the outer HTML text of element.
`innertext : string` | Read or write the inner HTML text of element.
`plaintext : string` | Read or write the plain text of element.
`find( string $selector [, int $index] ) : mixed` | Find children by the CSS selector. Returns the Nth element object if index is set, otherwise return an array of object.

# DOM traversing

Name | Description
---- | -----------
`$e->children( [int $index] ) : mixed` | Returns the Nth child object if index is set, otherwise return an array of children.
`$e->parent() : element` | Returns the parent of element.
`$e->first_child() : element` | Returns the first child of element, or null if not found.
`$e->last_child() : element` | Returns the last child of element, or null if not found.
`$e->next_sibling() : element` | Returns the next sibling of element, or null if not found.
`$e->prev_sibling() : element` | Returns the previous sibling of element, or null if not found.

# Camel naming conventions

Method | Mapping
------ | -------
`$e->getAllAttributes()` | `$e->attr`
`$e->getAttribute( $name )` | `$e->attribute`
`$e->setAttribute( $name, $value)` | `$value = $e->attribute`
`$e->hasAttribute( $name )` | `isset($e->attribute)`
`$e->removeAttribute ( $name )` | `$e->attribute = null`
`$e->getElementById ( $id )` | `$e->find ( "#$id", 0 )`
`$e->getElementsById ( $id [,$index] )` | `$e->find ( "#$id" [, int $index] )`
`$e->getElementByTagName ($name )` | `$e->find ( $name, 0 )`
`$e->getElementsByTagName ( $name [, $index] )` | `$e->find ( $name [, int $index] )`
`$e->parentNode ()` | `$e->parent ()`
`$e->childNodes ( [$index] )` | `$e->children ( [int $index] )`
`$e->firstChild ()` | `$e->first_child ()`
`$e->lastChild ()` | `$e->last_child ()`
`$e->nextSibling ()` | `$e->next_sibling ()`
`$e->previousSibling ()` | `$e->prev_sibling ()`
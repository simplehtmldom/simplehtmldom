The definitions below are an essential part of the parser.

## Node Types

The type of node is determined during parsing and represented by one of the elements in the list below.

| Type                | Description                                                         |
|---------------------|---------------------------------------------------------------------|
| `HDOM_TYPE_ELEMENT` | Start tag (i.e. `<html>`)                                           |
| `HDOM_TYPE_COMMENT` | HTML comment (i.e. `<!-- Hello, World! -->`)                        |
| `HDOM_TYPE_TEXT`    | Plain text (i.e. `Hello, World!`)                                   |
| `HDOM_TYPE_ROOT`    | Root element. There can always only be one root element in the DOM. |
| `HDOM_TYPE_UNKNOWN` | Unknown type (i.e. CDATA, DOCTYPE, etc...)                          |

### Example

```html
<!DOCTYPE html><html><!-- Hello, World! --></html>Hello, World!
```

_Note_: `HDOM_TYPE_ROOT` always exists regardless of the actual document structure.

| HTML                     | Node Type           |
|--------------------------|---------------------|
|                          | `HDOM_TYPE_ROOT`    |
| `<!DOCTYPE html>`        | `HDOM_TYPE_UNKNOWN` |
| `<html>`                 | `HDOM_TYPE_ELEMENT` |
| `<!-- Hello, World! -->` | `HDOM_TYPE_COMMENT` |
| `Hello, World!`          | `HDOM_TYPE_TEXT`    |

## Quote Types

Identifies the quoting type on attribute values.

| Type                | Description          |
|---------------------|----------------------|
| `HDOM_QUOTE_DOUBLE` | Double quotes (`""`) |
| `HDOM_QUOTE_SINGLE` | Single quotes (`''`) |
| `HDOM_QUOTE_NO`     | Not quoted (flag)    |

_Note_: Attributes with no values (flags) are stored as `HDOM_QUOTE_NO`.

### Example

```html
<p class="paragraph" id='info1' hidden>Hello, World!</p>
```

| Attribute           | Description         |
|---------------------|---------------------|
| `class="paragraph"` | `HDOM_QUOTE_DOUBLE` |
| `id='info1'`        | `HDOM_QUOTE_SINGLE` |
| `hidden`            | `HDOM_QUOTE_NO`     |

## Node Info Types

Each node stores additional information (metadata) that is identified by the elements below.

| Type                 | Description                                                                                                        |
|----------------------|--------------------------------------------------------------------------------------------------------------------|
| `HDOM_INFO_BEGIN`    | Cursor position for the start tag of a node.                                                                       |
| `HDOM_INFO_END`      | Cursor position for the end tag of a node. A value of zero indicates a node with no end tag (missing closing tag). |
| `HDOM_INFO_QUOTE`    | Quote type for attribute values. The value must be an element of [Quote Type](#quote-types).                       |
| `HDOM_INFO_SPACE`    | Array of whitespace around attributes (see [Attribute Whitespace](#attribute-whitespace)).                         |
| `HDOM_INFO_TEXT`     | Non-HTML text in tags (i.e. comments, doctype, etc...).                                                            |
| `HDOM_INFO_INNER`    | Inner text of a node.                                                                                              |
| `HDOM_INFO_OUTER`    | Outer text of a node.                                                                                              |
| `HDOM_INFO_ENDSPACE` | Whitespace at the end of a tag before the closing bracket.                                                         |

## Attribute Whitespace

Whitespace around attributes is stored in the form of an array with three elements:

| Element | Description                                               |
|---------|-----------------------------------------------------------|
| `0`     | Whitespace before the attribute name.                     |
| `1`     | Whitespace between attribute name and the equal sign.     |
| `2`     | Whitespace between the equal sign and the attribute value |

### Example

```html
<p class="paragraph" id = 'info1'hidden>Hello, World!</p>
```

_Note_: Whitespace before attribute names is not displayed in the browser. It is, however, part of the attributes.

| Attribute            | Description                          |
|----------------------|--------------------------------------|
| ` class="paragraph"` | `[0] => ' ', [1] => '', [2] => ''`   |
| ` id = 'info1'`      | `[0] => ' ', [1] => ' ', [2] => ' '` |
| `hidden`             | `[0] => '', [1] => '', [2] => ''`    |
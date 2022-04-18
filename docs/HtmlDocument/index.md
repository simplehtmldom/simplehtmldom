Represents the [DOM](https://en.wikipedia.org/wiki/Document_Object_Model) in memory. Provides functions to parse documents and access individual elements (see [`HtmlNode`](../HtmlNode/index.md)).

## Public Properties

| Property            | Description                                                                             |
|---------------------|-----------------------------------------------------------------------------------------|
| `root`              | Root node of the document.                                                              |
| `nodes`             | List of top-level nodes in the document.                                                |
| `callback`          | Callback function that is called for each element in the DOM when generating outertext. |
| `lowercase`         | If enabled, all tag names are converted to lowercase when parsing documents.            |
| `original_size`     | Original document size in bytes.                                                        |
| `size`              | Current document size in bytes.                                                         |
| `_charset`          | Charset of the original document.                                                       |
| `_target_charset`   | Target charset for the current document.                                                |
| `default_br_text`   | Text to return for `<br>` elements.                                                     |
| `default_span_text` | Text to return for `<span>` elements.                                                   |

## Protected Properties

| Property                | Description                                                                                                                                                  |
|-------------------------|--------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `pos`                   | Current parsing position within `doc`.                                                                                                                       |
| `doc`                   | The original document.                                                                                                                                       |
| `char`                  | Character at position `pos` in `doc`.                                                                                                                        |
| `cursor`                | Current element cursor in the document.                                                                                                                      |
| `parent`                | Parent element node.                                                                                                                                         |
| `noise`                 | Noise from the original document (i.e. scripts, comments, etc...).                                                                                           |
| `token_blank`           | Tokens that are considered whitespace in HTML.                                                                                                               |
| `block_tags`            | A list of tag names where remaining unclosed tags are forcibly closed.                                                                                       |
| `optional_closing_tags` | A list of tag names where the closing tag can be omitted.                                                                                                    |
---
title: simple_html_dom
---

# simple_html_dom

Represents the [DOM](https://en.wikipedia.org/wiki/Document_Object_Model) in memory. Provides functions to parse documents and access individual elements (see [`simple_html_dom_node`](../simple_html_dom_node/simple_html_dom_node.md)).

# Public Properties

| Property              | Description
| --------              | -----------
| `root`                | Root node of the document.
| `nodes`               | List of top-level nodes in the document.
| `callback`            | Callback function that is called for each element in the DOM when generating outertext.
| `lowercase`           | If enabled, all tag names are converted to lowercase when parsing documents.
| `original_size`       | Original document size in bytes.
| `size`                | Current document size in bytes.
| `_charset`            | Charset of the original document.
| `_target_charset`     | Target charset for the current document.
| `default_span_text`   | Text to return for `<span>` elements.

# Protected Properties

| Property                  | Description
| --------                  | -----------
| `pos`                     | Current parsing position within `doc`.
| `doc`                     | The original document.
| `char`                    | Character at position `pos` in `doc`.
| `cursor`                  | Current element cursor in the document.
| `parent`                  | Parent element node.
| `noise`                   | Noise from the original document (i.e. scripts, comments, etc...).
| `token_blank`             | Tokens that are considered whitespace in HTML.
| `token_equal`             | Tokens to identify the equal sign for attributes, stopping either at the closing tag ("/" i.e. `<html />`) or the end of an opening tag (">" i.e. `<html>`).
| `token_slash`             | Tokens to identify the end of a tag name. A tag name either ends on the ending slash ("/" i.e. `<html/>`) or whitespace (`"\s\r\n\t"`).
| `token_attr`              | Tokens to identify the end of an attribute.
| `default_br_text`         | Text to return for `<br>` elements.
| `self_closing_tags`       | A list of tag names where the closing tag is omitted.
| `block_tags`              | A list of tag names where remaining unclosed tags are forcibly closed.
| `optional_closing_tags`   | A list of tag names where the closing tag can be omitted.
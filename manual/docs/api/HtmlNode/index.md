---
title: HtmlNode
---

Represents a single node in the DOM tree (see [`HtmlDocument`](../HtmlDocument/)).

## Public Properties

| Property      | Description
| --------      | -----------
| `_`           | Node meta data (i.e. type of node).
| `attr`        | List of attributes.
| `children`    | List of child nodes.
| `nodes`       | List of nodes.
| `nodetype`    | Node type.
| `parent`      | Parent node object.
| `tag`         | Node's tag name.
| `tag_start`   | Start position of the tag name in the original document.

## Protected Properties

None.

## Private Properties

| Property      | Description
| --------      | -----------
| `dom`         | The DOM object (see [`HtmlDocument`](../HtmlDocument/)).
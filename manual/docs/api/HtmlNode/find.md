---
title: find
---

```php
find (
    string $selector
    [, int $idx = null ]
    [, bool $lowercase = false ]
) : mixed
```

| Parameter     | Description
| ---------     | -----------
| `selector`    | [CSS](https://www.w3.org/TR/selectors/) selector.
| `idx`         | Index of element to return.
| `lowercase`   | Matches tag names case insensitive (lowercase) if enabled.

Finds one or more nodes in the current document, using CSS selectors.

* Returns null if no match was found.
* Returns an array of [`HtmlNode`](../HtmlNode/) if `$idx` is null.
* Returns an object of type [`HtmlNode`](../HtmlNode/) if `$idx` is anything __but__ null.

## Supported Selectors

| Selector              | Description
| ---------             | -----------
| `*`                   | [Universal selector](https://www.w3.org/TR/selectors/#the-universal-selector)
| `E`                   | [Type (tag name) selector](https://www.w3.org/TR/selectors/#type-selectors)
| `E#id`                | [ID selector](https://www.w3.org/TR/selectors/#id-selectors)
| `E.class`             | [Class selector](https://www.w3.org/TR/selectors/#class-html)
| `E[attr]`             | [Attribute selector](https://www.w3.org/TR/selectors/#attribute-selectors)
| `E[attr="value"]`     | [Attribute selector](https://www.w3.org/TR/selectors/#attribute-selectors)
| `E[attr="value"] i`   | [Case-sensitivity](https://www.w3.org/TR/selectors/#attribute-case)
| `E[attr="value"] s`   | [Case-sensitivity](https://www.w3.org/TR/selectors/#attribute-case)
| `E[attr~="value"]`    | [Attribute selector](https://www.w3.org/TR/selectors/#attribute-selectors)
| `E[attr^="value"]`    | [Substring matching attribute selector](https://www.w3.org/TR/selectors/#attribute-substrings)
| `E[attr$="value"]`    | [Substring matching attribute selector](https://www.w3.org/TR/selectors/#attribute-substrings)
| `E[attr*="value"]`    | [Substring matching attribute selector](https://www.w3.org/TR/selectors/#attribute-substrings)
| `E[attr|="value"]`    | [Attribute selector](https://www.w3.org/TR/selectors/#attribute-selectors)
| `E F`                 | [Descendant combinator](https://www.w3.org/TR/selectors/#descendant-combinators)
| `E > F`               | [Child combinator](https://www.w3.org/TR/selectors/#child-combinators)
| `E + F`               | [Next-sibling combinator](https://www.w3.org/TR/selectors/#adjacent-sibling-combinators)
| `E ~ F`               | [Subsequent-sibling combinator](https://www.w3.org/TR/selectors/#general-sibling-combinators)
| `E, F`                | [Selector list](https://www.w3.org/TR/selectors/#selector-list)
| `E:not(s)`            | [Negation pseudo-class](https://www.w3.org/TR/selectors-3/#negation)
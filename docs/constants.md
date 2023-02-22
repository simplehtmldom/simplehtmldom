---
title: Constants
---

Constants define how the parser treats documents. They can be defined before
loading the parser to globally replace the default values.

## DEFAULT_TARGET_CHARSET

Defines the default target charset for text returned by the parser.

Default: `'UTF-8'`

## DEFAULT_BR_TEXT

Defines the default text to return for `<br>` elements.

Default: `"\r\n"`

## DEFAULT_SPAN_TEXT

Defines the default text to return for `<span>` elements.

Default: `' '`

## MAX_FILE_SIZE

Defines the maximum number of bytes the parser can load into memory. This limit
only applies to the source file or string.

Default: `2621440`
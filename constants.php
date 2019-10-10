<?php namespace simplehtmldom;

/**
 * Website: http://sourceforge.net/projects/simplehtmldom/
 * Additional projects: http://sourceforge.net/projects/debugobject/
 * Acknowledge: Jose Solorzano (https://sourceforge.net/projects/php-html/)
 *
 * Licensed under The MIT License
 * See the LICENSE file in the project root for more information.
 *
 * Authors:
 *   S.C. Chen
 *   John Schlick
 *   Rus Carroll
 *   logmanoriginal
 *
 * Contributors:
 *   Yousuke Kumakura
 *   Vadim Voituk
 *   Antcs
 *
 * Version $Rev$
 */

define(__NAMESPACE__ . '\HDOM_TYPE_ELEMENT', 1);
define(__NAMESPACE__ . '\HDOM_TYPE_COMMENT', 2);
define(__NAMESPACE__ . '\HDOM_TYPE_TEXT', 3);
define(__NAMESPACE__ . '\HDOM_TYPE_ENDTAG', 4);
define(__NAMESPACE__ . '\HDOM_TYPE_ROOT', 5);
define(__NAMESPACE__ . '\HDOM_TYPE_UNKNOWN', 6);
define(__NAMESPACE__ . '\HDOM_QUOTE_DOUBLE', 0);
define(__NAMESPACE__ . '\HDOM_QUOTE_SINGLE', 1);
define(__NAMESPACE__ . '\HDOM_QUOTE_NO', 3);
define(__NAMESPACE__ . '\HDOM_INFO_BEGIN', 0);
define(__NAMESPACE__ . '\HDOM_INFO_END', 1);
define(__NAMESPACE__ . '\HDOM_INFO_QUOTE', 2);
define(__NAMESPACE__ . '\HDOM_INFO_SPACE', 3);
define(__NAMESPACE__ . '\HDOM_INFO_TEXT', 4);
define(__NAMESPACE__ . '\HDOM_INFO_INNER', 5);
define(__NAMESPACE__ . '\HDOM_INFO_OUTER', 6);
define(__NAMESPACE__ . '\HDOM_INFO_ENDSPACE', 7);

defined(__NAMESPACE__ . '\DEFAULT_TARGET_CHARSET') || define(__NAMESPACE__ . '\DEFAULT_TARGET_CHARSET', 'UTF-8');
defined(__NAMESPACE__ . '\DEFAULT_BR_TEXT') || define(__NAMESPACE__ . '\DEFAULT_BR_TEXT', "\r\n");
defined(__NAMESPACE__ . '\DEFAULT_SPAN_TEXT') || define(__NAMESPACE__ . '\DEFAULT_SPAN_TEXT', ' ');
defined(__NAMESPACE__ . '\MAX_FILE_SIZE') || define(__NAMESPACE__ . '\MAX_FILE_SIZE', 2621440);
define(__NAMESPACE__ . '\HDOM_SMARTY_AS_TEXT', 1);

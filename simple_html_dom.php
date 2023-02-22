<?php

/**
 * Website: http://sourceforge.net/projects/simplehtmldom/
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

use simplehtmldom\HtmlNode;

if (defined('DEFAULT_TARGET_CHARSET')) {
	define('simplehtmldom\DEFAULT_TARGET_CHARSET', DEFAULT_TARGET_CHARSET);
}

if (defined('DEFAULT_BR_TEXT')) {
	define('simplehtmldom\DEFAULT_BR_TEXT', DEFAULT_BR_TEXT);
}

if (defined('DEFAULT_SPAN_TEXT')) {
	define('simplehtmldom\DEFAULT_SPAN_TEXT', DEFAULT_SPAN_TEXT);
}

if (defined('MAX_FILE_SIZE')) {
	define('simplehtmldom\MAX_FILE_SIZE', MAX_FILE_SIZE);
}

include_once 'HtmlDocument.php';
include_once 'HtmlNode.php';

if (!defined('DEFAULT_TARGET_CHARSET')) {
	define('DEFAULT_TARGET_CHARSET', \simplehtmldom\DEFAULT_TARGET_CHARSET);
}

if (!defined('DEFAULT_BR_TEXT')) {
	define('DEFAULT_BR_TEXT', \simplehtmldom\DEFAULT_BR_TEXT);
}

if (!defined('DEFAULT_SPAN_TEXT')) {
	define('DEFAULT_SPAN_TEXT', \simplehtmldom\DEFAULT_SPAN_TEXT);
}

if (!defined('MAX_FILE_SIZE')) {
	define('MAX_FILE_SIZE', \simplehtmldom\MAX_FILE_SIZE);
}

const HDOM_TYPE_ELEMENT = HtmlNode::HDOM_TYPE_ELEMENT;
const HDOM_TYPE_COMMENT = HtmlNode::HDOM_TYPE_COMMENT;
const HDOM_TYPE_TEXT = HtmlNode::HDOM_TYPE_TEXT;
const HDOM_TYPE_ROOT = HtmlNode::HDOM_TYPE_ROOT;
const HDOM_TYPE_UNKNOWN = HtmlNode::HDOM_TYPE_UNKNOWN;
const HDOM_QUOTE_DOUBLE = HtmlNode::HDOM_QUOTE_DOUBLE;
const HDOM_QUOTE_SINGLE = HtmlNode::HDOM_QUOTE_SINGLE;
const HDOM_QUOTE_NO = HtmlNode::HDOM_QUOTE_NO;
const HDOM_INFO_BEGIN = HtmlNode::HDOM_INFO_BEGIN;
const HDOM_INFO_END = HtmlNode::HDOM_INFO_END;
const HDOM_INFO_QUOTE = HtmlNode::HDOM_INFO_QUOTE;
const HDOM_INFO_SPACE = HtmlNode::HDOM_INFO_SPACE;
const HDOM_INFO_TEXT = HtmlNode::HDOM_INFO_TEXT;
const HDOM_INFO_INNER = HtmlNode::HDOM_INFO_INNER;
const HDOM_INFO_OUTER = HtmlNode::HDOM_INFO_OUTER;
const HDOM_INFO_ENDSPACE = HtmlNode::HDOM_INFO_ENDSPACE;

const HDOM_SMARTY_AS_TEXT = \simplehtmldom\HDOM_SMARTY_AS_TEXT;

class_alias('\simplehtmldom\HtmlDocument', 'simple_html_dom');
class_alias('\simplehtmldom\HtmlNode', 'simple_html_dom_node');

function file_get_html(
	$url,
	$use_include_path = false,
	$context = null,
	$offset = 0,
	$maxLen = -1,
	$lowercase = true,
	$forceTagsClosed = true,
	$target_charset = DEFAULT_TARGET_CHARSET,
	$stripRN = true,
	$defaultBRText = DEFAULT_BR_TEXT,
	$defaultSpanText = DEFAULT_SPAN_TEXT)
{
	if($maxLen <= 0) { $maxLen = MAX_FILE_SIZE; }

	$dom = new simple_html_dom(
		null,
		$lowercase,
		$forceTagsClosed,
		$target_charset,
		$stripRN,
		$defaultBRText,
		$defaultSpanText
	);

	$contents = file_get_contents(
		$url,
		$use_include_path,
		$context,
		$offset,
		$maxLen + 1 // Load extra byte for limit check
	);

	if (empty($contents) || strlen($contents) > $maxLen) {
		$dom->clear();
		return false;
	}

	return $dom->load($contents, $lowercase, $stripRN);
}

function str_get_html(
	$str,
	$lowercase = true,
	$forceTagsClosed = true,
	$target_charset = DEFAULT_TARGET_CHARSET,
	$stripRN = true,
	$defaultBRText = DEFAULT_BR_TEXT,
	$defaultSpanText = DEFAULT_SPAN_TEXT)
{
	$dom = new simple_html_dom(
		null,
		$lowercase,
		$forceTagsClosed,
		$target_charset,
		$stripRN,
		$defaultBRText,
		$defaultSpanText
	);

	if (empty($str) || strlen($str) > MAX_FILE_SIZE) {
		$dom->clear();
		return false;
	}

	return $dom->load($str, $lowercase, $stripRN);
}

/** @codeCoverageIgnore */
function dump_html_tree($node, $show_attr = true, $deep = 0)
{
	$node->dump($node);
}

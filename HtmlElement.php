<?php
/*
 * Copyright (c) 2022, logmanoriginal
 *
 * SPDX-License-Identifier: MIT
 */

namespace simplehtmldom;

class HtmlElement
{
	const A = 'a';
	const ABBR = 'abbr';
	const ADDRESS = 'address';
	const AREA = 'area';
	const ARTICLE = 'article';
	const ASIDE = 'aside';
	const AUDIO = 'audio';
	const B = 'b';
	const BASE = 'base';
	const BDI = 'bdi';
	const BDO = 'bdo';
	const BLOCKQUOTE = 'blockquote';
	const BR = 'br';
	const BUTTON = 'button';
	const CANVAS = 'canvas';
	const CITE = 'cite';
	const CODE = 'code';
	const COL = 'col';
	const DATA = 'data';
	const DATALIST = 'datalist';
	const DEL = 'del';
	const DETAILS = 'details';
	const DFN = 'dfn';
	const DIV = 'div';
	const DL = 'dl';
	const EM = 'em';
	const EMBED = 'embed';
	const FIELDSET = 'fieldset';
	const FIGURE = 'figure';
	const FOOTER = 'footer';
	const FORM = 'form';
	const H1 = 'h1';
	const H2 = 'h2';
	const H3 = 'h3';
	const H4 = 'h4';
	const H5 = 'h5';
	const H6 = 'h6';
	const HEADER = 'header';
	const HGROUP = 'hgroup';
	const HR = 'hr';
	const I = 'i';
	const IFRAME = 'iframe';
	const IMG = 'img';
	const INPUT = 'input';
	const INS = 'ins';
	const KBD = 'kbd';
	const LABEL = 'label';
	const LINK = 'link';
	const MAIN = 'main';
	const MAP = 'map';
	const MARK = 'mark';
	const MATH = 'math';
	const MENU = 'menu';
	const META = 'meta';
	const METER = 'meter';
	const NAV = 'nav';
	const NOSCRIPT = 'noscript';
	const OBJECT = 'object';
	const OL = 'ol';
	const OUTPUT = 'output';
	const P = 'p';
	const PARAM = 'param';
	const PICTURE = 'picture';
	const PRE = 'pre';
	const PROGRESS = 'progress';
	const Q = 'q';
	const RUBY = 'ruby';
	const S = 's';
	const SAMP = 'samp';
	const SCRIPT = 'script';
	const SECTION = 'section';
	const SELECT = 'select';
	const SLOT = 'slot';
	const SMALL = 'small';
	const SOURCE = 'source';
	const SPAN = 'span';
	const STRONG = 'strong';
	const STYLE = 'style';
	const SUB = 'sub';
	const SUP = 'sup';
	const SVG = 'svg';
	const TABLE = 'table';
	const TEMPLATE = 'template';
	const TEXTAREA = 'textarea';
	const TIME = 'time';
	const TITLE = 'title';
	const TRACK = 'track';
	const U = 'u';
	const UL = 'ul';
	// TODO: Rename '_VAR' to 'VAR' when changing language level to PHP 7.0+
	const _VAR = 'var';
	const VIDEO = 'video';
	const WBR = 'wbr';

	// https://html.spec.whatwg.org/multipage/dom.html#embedded-content-2
	static function isEmbeddedContent($element)
	{
		$element = strtolower($element);

		return $element === self::AUDIO
			|| $element === self::CANVAS
			|| $element === self::EMBED
			|| $element === self::IFRAME
			|| $element === self::IMG
			|| $element === self::MATH
			|| $element === self::OBJECT
			|| $element === self::PICTURE
			|| $element === self::SVG
			|| $element === self::VIDEO;
	}

	// https://html.spec.whatwg.org/multipage/dom.html#heading-content
	static function isHeadingContent($element)
	{
		$element = strtolower($element);

		return $element === self::H1
			|| $element === self::H2
			|| $element === self::H3
			|| $element === self::H4
			|| $element === self::H5
			|| $element === self::H6
			|| $element === self::HGROUP;
	}

	// https://html.spec.whatwg.org/multipage/dom.html#interactive-content
	static function isInteractiveContent($element)
	{
		$element = strtolower($element);

		return $element === self::A
			|| $element === self::AUDIO
			|| $element === self::BUTTON
			|| $element === self::DETAILS
			|| $element === self::EMBED
			|| $element === self::IFRAME
			|| $element === self::IMG
			|| $element === self::INPUT
			|| $element === self::LABEL
			|| $element === self::SELECT
			|| $element === self::TEXTAREA
			|| $element === self::VIDEO;
	}

	// https://html.spec.whatwg.org/multipage/dom.html#metadata-content
	static function isMetadataContent($element)
	{
		$element = strtolower($element);

		return $element === self::BASE
			|| $element === self::LINK
			|| $element === self::META
			|| $element === self::NOSCRIPT
			|| $element === self::SCRIPT
			|| $element === self::STYLE
			|| $element === self::TEMPLATE
			|| $element === self::TITLE;
	}

	// https://html.spec.whatwg.org/multipage/dom.html#palpable-content
	static function isPalpableContent($element)
	{
		$element = strtolower($element);

		return $element === self::A
			|| $element === self::ABBR
			|| $element === self::ADDRESS
			|| $element === self::ARTICLE
			|| $element === self::ASIDE
			|| $element === self::AUDIO
			|| $element === self::B
			|| $element === self::BDI
			|| $element === self::BDO
			|| $element === self::BLOCKQUOTE
			|| $element === self::BUTTON
			|| $element === self::CANVAS
			|| $element === self::CITE
			|| $element === self::CODE
			|| $element === self::DATA
			|| $element === self::DETAILS
			|| $element === self::DFN
			|| $element === self::DIV
			|| $element === self::DL
			|| $element === self::EM
			|| $element === self::EMBED
			|| $element === self::FIELDSET
			|| $element === self::FIGURE
			|| $element === self::FOOTER
			|| $element === self::FORM
			|| $element === self::H1
			|| $element === self::H2
			|| $element === self::H3
			|| $element === self::H4
			|| $element === self::H5
			|| $element === self::H6
			|| $element === self::HEADER
			|| $element === self::HGROUP
			|| $element === self::I
			|| $element === self::IFRAME
			|| $element === self::IMG
			|| $element === self::INPUT
			|| $element === self::INS
			|| $element === self::KBD
			|| $element === self::LABEL
			|| $element === self::MAIN
			|| $element === self::MAP
			|| $element === self::MARK
			|| $element === self::MATH
			|| $element === self::MENU
			|| $element === self::METER
			|| $element === self::NAV
			|| $element === self::OBJECT
			|| $element === self::OL
			|| $element === self::OUTPUT
			|| $element === self::P
			|| $element === self::PRE
			|| $element === self::PROGRESS
			|| $element === self::Q
			|| $element === self::RUBY
			|| $element === self::S
			|| $element === self::SAMP
			|| $element === self::SECTION
			|| $element === self::SELECT
			|| $element === self::SMALL
			|| $element === self::SPAN
			|| $element === self::STRONG
			|| $element === self::SUB
			|| $element === self::SUP
			|| $element === self::SVG
			|| $element === self::TABLE
			|| $element === self::TEXTAREA
			|| $element === self::TIME
			|| $element === self::U
			|| $element === self::UL
			|| $element === self::_VAR
			|| $element === self::VIDEO;
	}

	// https://html.spec.whatwg.org/multipage/dom.html#phrasing-content
	static function isPhrasingContent($element)
	{
		$element = strtolower($element);

		return $element === self::A
			|| $element === self::ABBR
			|| $element === self::AREA
			|| $element === self::AUDIO
			|| $element === self::B
			|| $element === self::BDI
			|| $element === self::BDO
			|| $element === self::BR
			|| $element === self::BUTTON
			|| $element === self::CANVAS
			|| $element === self::CITE
			|| $element === self::CODE
			|| $element === self::DATA
			|| $element === self::DATALIST
			|| $element === self::DEL
			|| $element === self::DFN
			|| $element === self::EM
			|| $element === self::EMBED
			|| $element === self::I
			|| $element === self::IFRAME
			|| $element === self::IMG
			|| $element === self::INPUT
			|| $element === self::INS
			|| $element === self::KBD
			|| $element === self::LABEL
			|| $element === self::LINK
			|| $element === self::MAP
			|| $element === self::MARK
			|| $element === self::MATH
			|| $element === self::META
			|| $element === self::METER
			|| $element === self::NOSCRIPT
			|| $element === self::OBJECT
			|| $element === self::OUTPUT
			|| $element === self::PICTURE
			|| $element === self::PROGRESS
			|| $element === self::Q
			|| $element === self::RUBY
			|| $element === self::S
			|| $element === self::SAMP
			|| $element === self::SCRIPT
			|| $element === self::SELECT
			|| $element === self::SLOT
			|| $element === self::SMALL
			|| $element === self::SPAN
			|| $element === self::STRONG
			|| $element === self::SUB
			|| $element === self::SUP
			|| $element === self::SVG
			|| $element === self::TEMPLATE
			|| $element === self::TEXTAREA
			|| $element === self::TIME
			|| $element === self::U
			|| $element === self::_VAR
			|| $element === self::VIDEO
			|| $element === self::WBR;
	}

	// https://html.spec.whatwg.org/multipage/dom.html#sectioning-content
	static function isSectioningContent($element)
	{
		$element = strtolower($element);

		return $element === self::ARTICLE
			|| $element === self::ASIDE
			|| $element === self::NAV
			|| $element === self::SECTION;
	}

	// https://html.spec.whatwg.org/multipage/syntax.html#raw-text-elements
	static function isRawTextElement($element)
	{
		$element = strtolower($element);

		return $element === self::SCRIPT
			|| $element === self::STYLE;
	}

	// https://html.spec.whatwg.org/multipage/syntax.html#void-elements
	static function isVoidElement($element)
	{
		$element = strtolower($element);

		return $element === self::AREA
			|| $element === self::BASE
			|| $element === self::BR
			|| $element === self::COL
			|| $element === self::EMBED
			|| $element === self::HR
			|| $element === self::IMG
			|| $element === self::INPUT
			|| $element === self::LINK
			|| $element === self::META
			|| $element === self::PARAM
			|| $element === self::SOURCE
			|| $element === self::TRACK
			|| $element === self::WBR;
	}
}

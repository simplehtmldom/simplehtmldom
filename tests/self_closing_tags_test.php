<?php
require_once __DIR__ . '/../simple_html_dom.php';
use PHPUnit\Framework\TestCase;

/**
 * Test identification of self closing tags (void elements)
 *
 * _Remarks_:
 * - Test functions are ordered by name to allow for simple navigation.
 * - Self-closing tags should be tested for their previous and next sibling, as
 * they are "consumed" by incorrectly detected elements. For example:
 * `<div id="before"></div><tag><div id="after"></div>` will result in
 * `tag->outertext` with contents `<tag><div id="after"></div>` because `<tag>`
 * is NOT a valid void element. If it were a valid element, `tag->outertext`
 * would return `<tag>` instead.
 * - Tests should not include more than one self-closing tag, to correctly assert
 * results.
 * - Do not remove obsolete tests! These tests should be changed to correctly
 * assert the opposite condition is true (i.e. change `assertNull` to
 * `assertNotNull`).
 *
 * _Code template_:
 * Use the following code template for all unit tests in this file. Replace all
 * occurrences of "tag" by the corresponding tag name (i.e. for the `area` tag
 * function name `test_tag` should become `test_area`).
 *
 * ```php
 * public function test_tag() {
 * 	$src = <<<HTML
 * <div id="before"></div>
 * <tag>
 * <div id="after"></div>
 * HTML;
 *
 * 	$tag = $this->html->load($src)->find('tag', 0);
 *
 * 	$this->assertEquals('before', $tag->previousSibling()->id);
 * 	$this->assertEquals('<tag>', $tag->outertext);
 * 	$this->assertEquals('after', $tag->nextSibling()->id);
 * }
 * ```
 *
 * @link https://www.w3.org/TR/html HTML Specification
 * @link https://www.w3.org/TR/html/syntax.html#void-elements Void elements
 */
class self_closing_tags_test extends TestCase {

	private $html;

	protected function setUp()
	{
		$this->html = new simple_html_dom;
	}

	protected function tearDown()
	{
		$this->html->clear();
		unset($this->html);
	}

	/**
	 * @link https://www.w3.org/TR/html52/semantics-embedded-content.html#the-area-element
	 * The area element
	 */
	public function test_area()
	{
		$src = <<<HTML
<map name="shapes">
	<div id="before"></div>
	<area shape=rect coords="50,50,100,100">
	<div id="after"></div>
</map>
HTML;

		$area = $this->html->load($src)->find('area', 0);

		$this->assertEquals('before', $area->previousSibling()->id);
		$this->assertEquals('<area shape=rect coords="50,50,100,100">', $area->outertext);
		$this->assertEquals('after', $area->nextSibling()->id);
	}

	/**
	 * @link https://www.w3.org/TR/html52/document-metadata.html#the-base-element
	 * The base element
	 */
	public function test_base()
	{
		$src = <<<HTML
<head>
	<div id="before"></div>
	<base href="http://simplehtmldom.sourceforge.net/" target="_blank">
	<div id="after"></div>
</head>
HTML;

		$base = $this->html->load($src)->find('base', 0);

		$this->assertEquals('before', $base->previousSibling()->id);
		$this->assertEquals('<base href="http://simplehtmldom.sourceforge.net/" target="_blank">', $base->outertext);
		$this->assertEquals('after', $base->nextSibling()->id);
	}

	/**
	 * @link https://www.w3.org/TR/html52/textlevel-semantics.html#the-br-element
	 * The br element
	 */
	public function test_br()
	{
		$src = <<<HTML
<body>
	<div id="before"></div>
	<p>PHP Simple HTML DOM Parser<br>A PHP based DOM parser</p>
	<div id="after"></div>
</body>
HTML;

		// Normal operation (innertext of <br> is replaced by DEFAULT_BR_TEXT)
		$br = $this->html->load($src)->find('br', 0);

		$this->assertEquals('before', $br->parentNode()->previousSibling()->id);
		$this->assertEquals('<br>', $br->outertext);
		$this->assertEquals(DEFAULT_BR_TEXT, $br->innertext);
		$this->assertEquals('after', $br->parentNode()->nextSibling()->id);

		$this->assertEquals(
			'PHP Simple HTML DOM Parser' . DEFAULT_BR_TEXT . 'A PHP based DOM parser',
			$br->parentNode()->plaintext
		);

		// Custom text (innertext of <br> is replaced by custom value)
		$br_text = 'br_text';
		$br = $this->html->load($src, true, true, $br_text)->find('br', 0);

		$this->assertEquals('before', $br->parentNode()->previousSibling()->id);
		$this->assertEquals('<br>', $br->outertext);
		$this->assertEquals($br_text, $br->innertext);
		$this->assertEquals('after', $br->parentNode()->nextSibling()->id);

		$this->assertEquals(
			'PHP Simple HTML DOM Parser' . $br_text . 'A PHP based DOM parser',
			$br->parentNode()->plaintext
		);
	}

	/**
	 * @link https://www.w3.org/TR/html52/tabular-data.html#the-col-element
	 * The col element
	 */
	public function test_col()
	{
		$src = <<<HTML
<table>
	<div id="before"></div>
	<colgroup><col><col><col></colgroup>
	<div id="after"></div>
</table>
HTML;

		$col = $this->html->load($src)->find('col', 0);

		$this->assertEquals('before', $col->parentNode()->previousSibling()->id);
		$this->assertEquals('<col>', $col->outertext);
		$this->assertEquals('after', $col->parentNode()->nextSibling()->id);
	}

	/**
	 * @link https://www.w3.org/TR/html52/semantics-embedded-content.html#the-embed-element
	 * The embed element
	 */
	public function test_embed()
	{
		$src = <<<HTML
<body>
	<div id="before"></div>
	<embed src="catgame.swf" quality="high">
	<div id="after"></div>
</body>
HTML;

		$embed = $this->html->load($src)->find('embed', 0);

		$this->assertEquals('before', $embed->previousSibling()->id);
		$this->assertEquals('<embed src="catgame.swf" quality="high">', $embed->outertext);
		$this->assertEquals('after', $embed->nextSibling()->id);
	}

	/**
	 * @link https://www.w3.org/TR/html52/grouping-content.html#the-hr-element
	 * The hr element
	 */
	public function test_hr()
	{
		$src = <<<HTML
<p>PHP Simple HTML DOM Parser</p>
<div id="before"></div>
<hr>
<div id="after"></div>
<p>A PHP based DOM parser</p>
HTML;

		$hr = $this->html->load($src)->find('hr', 0);

		$this->assertEquals('before', $hr->previousSibling()->id);
		$this->assertEquals('<hr>', $hr->outertext);
		$this->assertEquals('after', $hr->nextSibling()->id);
	}

	/**
	 * @link https://www.w3.org/TR/html52/semantics-embedded-content.html#the-img-element
	 * The img element
	 */
	public function test_img()
	{
		$src = <<<HTML
<a href="http://simplehtmldom.sourceforge.net">
<div id="before"></div>
<img src="images/simple_html_dom.png" width="128" height="128" alt="PHP Simple HTML DOM Parser Manual">
<div id="after"></div>
</a>
HTML;

		$img = $this->html->load($src)->find('img', 0);

		$this->assertEquals('before', $img->previousSibling()->id);
		$this->assertEquals(
			'<img src="images/simple_html_dom.png" width="128" height="128" alt="PHP Simple HTML DOM Parser Manual">',
			$img->outertext);
		$this->assertEquals('after', $img->nextSibling()->id);
	}

	/**
	 * @link https://www.w3.org/TR/html52/sec-forms.html#the-input-element
	 * The input element
	 */
	public function test_input()
	{
		$src = <<<HTML
<body>
	<div id="before"></div>
	<input type="url" name="location" list="urls">
	<div id="after"></div>
	<datalist id="urls">
		<option
			label="PHP Simple HTML DOM Parser"
			value="https://sourceforge.net/projects/simplehtmldom/"></option>
		<option
			label="PHP Simple HTML DOM Parser Manual"
			value="http://simplehtmldom.sourceforge.net/"></option>
	</datalist>
</body>
HTML;

		$input = $this->html->load($src)->find('input', 0);

		$this->assertEquals('before', $input->previousSibling()->id);
		$this->assertEquals('<input type="url" name="location" list="urls">', $input->outertext);
		$this->assertEquals('after', $input->nextSibling()->id);
	}

	/**
	 * @link https://www.w3.org/TR/html52/document-metadata.html#the-link-element
	 * The link element
	 */
	public function test_link()
	{
		$src = <<<HTML
<title>
	<div id="before"></div>
	<link rel="MIT license" href="https://opensource.org/licenses/MIT">
	<div id="after"></div>
<title>
HTML;

		$link = $this->html->load($src)->find('link', 0);

		$this->assertEquals('before', $link->previousSibling()->id);
		$this->assertEquals('<link rel="MIT license" href="https://opensource.org/licenses/MIT">', $link->outertext);
		$this->assertEquals('after', $link->nextSibling()->id);
	}

	/**
	 * @link https://www.w3.org/TR/html52/document-metadata.html#the-meta-element
	 * The meta element
	 */
	public function test_meta()
	{
		$src = <<<HTML
<title>
	<div id="before"></div>
	<meta name=generator content="Simple HTML DOM Parser">
	<div id="after"></div>
</title>
HTML;

		$meta = $this->html->load($src)->find('meta', 0);

		$this->assertEquals('before', $meta->previousSibling()->id);
		$this->assertEquals('<meta name=generator content="Simple HTML DOM Parser">', $meta->outertext);
		$this->assertEquals('after', $meta->nextSibling()->id);
	}

	/**
	 * @link https://www.w3.org/TR/html52/semantics-embedded-content.html#the-param-element
	 * The param element
	 */
	public function test_param()
	{
		$src = <<<HTML
<object type="application/simple_html_dom">
	<div id="before"></div>
	<param name="self_closing_tags" value="param">
	<div id="after"></div>
</object>
HTML;

		$param = $this->html->load($src)->find('param', 0);

		$this->assertEquals('before', $param->previousSibling()->id);
		$this->assertEquals('<param name="self_closing_tags" value="param">', $param->outertext);
		$this->assertEquals('after', $param->nextSibling()->id);
	}

	/**
	 * @link https://www.w3.org/TR/html52/semantics-embedded-content.html#the-source-element
	 * The source element
	 */
	public function test_source()
	{
		$src = <<<HTML
<video controls autoplay>
	<div id="before"></div>
	<source src="simple_html_dom.mp4" type="video/mp4">
	<div id="after"></div>
</video>
HTML;

		$source = $this->html->load($src)->find('source', 0);

		$this->assertEquals('before', $source->previousSibling()->id);
		$this->assertEquals('<source src="simple_html_dom.mp4" type="video/mp4">', $source->outertext);
		$this->assertEquals('after', $source->nextSibling()->id);
	}

	/**
	 * Checks if the spacer element is NOT detected as self-closing tag
	 *
	 * @link https://www.w3.org/TR/html5/obsolete.html#non-conforming-features
	 * Non-conforming features
	 * @link https://www.w3.org/TR/html5/dom.html#ref-for-elementdef-spacer
	 * Elements in the DOM
	 * @link https://developer.mozilla.org/en-US/docs/Web/HTML/Element/spacer
	 * <spacer>
	 */
	public function test_spacer_not()
	{
		$src = <<<HTML
<div id="before"></div>
<spacer type="horizontal" size="10"></spacer>
<div id="after"></div>
HTML;

		$spacer = $this->html->load($src)->find('spacer', 0);

		$this->assertEquals('before', $spacer->previousSibling()->id);
		$this->assertEquals('<spacer type="horizontal" size="10"></spacer>', $spacer->outertext);
		$this->assertEquals('after', $spacer->nextSibling()->id);
	}

	/**
	 * @link https://www.w3.org/TR/html52/semantics-embedded-content.html#the-track-element
	 * The track element
	 */
	public function test_track()
	{
		$src = <<<HTML
<video src="simple_html_dom.webm">
	<div id="before"></div>
	<track kind=subtitles src=simple_html_dom.en.vtt srclang=en label="English">
	<div id="after"></div>
	<track kind=captions src=simple_html_dom.hoh.vtt srclang=en label="English captions">
	<track kind=subtitles src=simple_html_dom.fr.vtt srclang=fr lang=fr label="Français">
	<track kind=subtitles src=simple_html_dom.de.vtt srclang=de lang=de label="Deutsch">
</video>
HTML;

		$track = $this->html->load($src)->find('track', 0);

		$this->assertEquals('before', $track->previousSibling()->id);

		$this->assertEquals(
			'<track kind=subtitles src=simple_html_dom.en.vtt srclang=en label="English">',
			$track->outertext
		);

		$this->assertEquals('after', $track->nextSibling()->id);
	}

	/**
	 * @link https://www.w3.org/TR/html52/textlevel-semantics.html#the-wbr-element
	 * The wbr element
	 */
	public function test_wbr()
	{
		$src = <<<HTML
<div id="before"></div>
<p>https://sourceforge.net/<wbr>projects/<wbr>simplehtmldom/</p>
<div id="after"></div>
HTML;

		$wbr = $this->html->load($src)->find('wbr', 0);

		$this->assertEquals('before', $wbr->parentNode()->previousSibling()->id);
		$this->assertEquals('<wbr>', $wbr->outertext);
		$this->assertEquals('after', $wbr->parentNode()->nextSibling()->id);
	}

}

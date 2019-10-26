<?php
require_once __DIR__ . '/../simple_html_dom.php';
use PHPUnit\Framework\TestCase;

/**
 * Tests for optional tags
 *
 * Optional tags are tags that can be omitted in cases where the DOM structure
 * is obvious to the parser.
 *
 * _Note_:
 * - Elements with optional end tag (test_optional_*_end_tag) should appear in
 * {@see simple_html_dom::$optional_closing_tags}
 *
 * @link https://www.w3.org/TR/html52/single-page.html#optional-tags
 * Optional tags
 */
class optional_tags_test extends TestCase {
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
	 * An html element’s start tag may be omitted if the first thing inside the
	 * html element is not a comment.
	 */
	public function test_optional_html_start_tag()
	{
		$doc = <<<HTML
<!DOCTYPE HTML>

<head></head>
<body></body>
</html>
HTML;

		$this->markTestSkipped(
			'Optional html start tag is not supported right now'
		);

		$this->assertNotNull($this->html->load($doc)->find('html', 0));
	}

	/**
	 * An html element’s end tag may be omitted if the html element is not
	 * immediately followed by a comment.
	 */
	public function test_optional_html_end_tag()
	{
		$doc = <<<HTML
<!DOCTYPE HTML>
<html>
<head></head>
<body></body>

HTML;

		$this->assertNotNull($this->html->load($doc)->find('html', 0));
	}

	/**
	 * A head element’s start tag may be omitted if the element is empty, or if
	 * the first thing inside the head element is an element.
	 */
	public function test_optional_head_start_tag()
	{
		$doc = <<<HTML
<!DOCTYPE HTML>
<html>
</head>
<body></body>
</html>
HTML;

		$this->markTestSkipped(
			'Optional head start tag is not supported right now'
		);

		$this->assertNotNull($this->html->load($doc)->find('head', 0));
	}

	/**
	 * A head element’s end tag may be omitted if the head element is not
	 * immediately followed by a space character or a comment.
	 */
	public function test_optional_head_end_tag()
	{
		$doc = <<<HTML
<!DOCTYPE HTML>
<html>
<head>
<body></body>
</html>
HTML;

		$this->assertNotNull($this->html->load($doc)->find('head', 0));
	}

	/**
	 * A body element’s start tag may be omitted if the element is empty, or if
	 * the first thing inside the body element is not a space character or a
	 * comment, except if the first thing inside the body element is a meta,
	 * link, script, style, or template element.
	 */
	public function test_optional_body_start_tag()
	{
		$doc = <<<HTML
<!DOCTYPE HTML>
<html>
<head></head>
</body>
</html>
HTML;

		$this->markTestSkipped(
			'Optional body start tag is not supported right now'
		);

		$this->assertNotNull($this->html->load($doc)->find('body', 0));
	}

	/**
	 * A body element’s end tag may be omitted if the body element is not
	 * immediately followed by a comment.
	 */
	public function test_optional_body_end_tag()
	{
		$doc = <<<HTML
<!DOCTYPE HTML>
<html>
<head></head>
<body>
</html>
HTML;

		$this->assertNotNull($this->html->load($doc)->find('body', 0));
	}

	/**
	 * With all optional tags taken into account, the DOM can be simplified
	 */
	public function test_optional_html_head_body_tags()
	{
		// phpcs:disable Generic.Files.LineLength
		$doc1 = <<<HTML
<!DOCTYPE HTML><html><head><title>PHP Simple HTML DOM Parser</title></head><body><p>A PHP based DOM parser</p></body></html>
HTML;
		// phpcs:enable

		$doc2 = <<<HTML
<!DOCTYPE HTML><title>PHP Simple HTML DOM Parser</title><p>A PHP based DOM parser</p>
HTML;

		$dom1 = $this->html->load($doc1);
		$dom2 = $this->html->load($doc2);

		$this->markTestSkipped(
			'Some optional tags are not supported right now'
		);

		$this->assertNotNull($dom1->find('html', 0), 'html not found!');
		$this->assertNotNull($dom1->find('head', 0), 'head not found!');
		$this->assertNotNull($dom1->find('body', 0), 'body not found!');

		$this->assertEquals($dom1->find('html', 0), $dom2->find('html', 0));
		$this->assertEquals($dom1->find('head', 0), $dom2->find('head', 0));
		$this->assertEquals($dom1->find('body', 0), $dom2->find('body', 0));
	}

	/**
	 * An li element’s end tag may be omitted if the li element is immediately
	 * followed by another li element or if there is no more content in the
	 * parent element.
	 */
	public function test_optional_li_end_tag()
	{
		$doc = <<<HTML
<!DOCTYPE HTML>
<html>
<head></head>
<body>
	<ul><li>PHP Simple HTML DOM Parser<li>A PHP based DOM parser</ul>
</body>
</html>
HTML;

		$dom = $this->html->load($doc);

		$this->assertCount(2, $dom->find('li'));
		$this->assertEquals('PHP Simple HTML DOM Parser', $dom->find('li', 0)->innertext);
		$this->assertEquals('A PHP based DOM parser', $dom->find('li', 1)->innertext);
	}

	/**
	 * A dt element’s end tag may be omitted if the dt element is immediately
	 * followed by another dt element or a dd element.
	 */
	public function test_optional_dt_end_tag()
	{
		$doc = <<<HTML
<!DOCTYPE HTML>
<html>
<head></head>
<body>
	<dl><dt>PHP Simple HTML DOM Parser<dt>A PHP based DOM parser</dl>
</body>
</html>
HTML;

		$dom = $this->html->load($doc);

		$this->assertCount(2, $dom->find('dt'));
		$this->assertEquals('PHP Simple HTML DOM Parser', $dom->find('dt', 0)->innertext);
		$this->assertEquals('A PHP based DOM parser', $dom->find('dt', 1)->innertext);
	}

	/**
	 * A dd element’s end tag may be omitted if the dd element is immediately
	 * followed by another dd element or a dt element, or if there is no more
	 * content in the parent element.
	 */
	public function test_optional_dd_end_tag()
	{
		$doc = <<<HTML
<!DOCTYPE HTML>
<html>
<head></head>
<body>
	<dl><dd>PHP Simple HTML DOM Parser<dd>A PHP based DOM parser</dl>
</body>
</html>
HTML;

		$dom = $this->html->load($doc);

		$this->assertCount(2, $dom->find('dd'));
		$this->assertEquals('PHP Simple HTML DOM Parser', $dom->find('dd', 0)->innertext);
		$this->assertEquals('A PHP based DOM parser', $dom->find('dd', 1)->innertext);
	}

	/**
	 * A p element’s end tag may be omitted if the p element is immediately
	 * followed by an address, article, aside, blockquote, details, div, dl,
	 * fieldset, figcaption, figure, footer, form, h1, h2, h3, h4, h5, h6,
	 * header, hr, main, nav, ol, p, pre, section, table, or ul element, or if
	 * there is no more content in the parent element and the parent element is
	 * an HTML element that is not an a, audio, del, ins, map, noscript, or
	 * video element, or an autonomous custom element.
	 */
	public function test_optional_p_end_tag()
	{
		$token = array(
			'address', 'article', 'aside', 'blockquote', 'details', 'div', 'dl',
			'fieldset', 'figcaption', 'figure', 'footer', 'form', 'h1', 'h2',
			'h3', 'h4', 'h5', 'h6', 'header', 'hr', 'main', 'nav', 'ol', 'p',
			'pre', 'section', 'table', 'ul'
		);

		foreach ($token as $t) {
			$doc = <<<HTML
<!DOCTYPE HTML>
<html>
<head></head>
<body>
	<div><p>PHP Simple HTML DOM Parser<{$t}>A PHP based DOM parser</{$t}></div>
</body>
</html>
HTML;

			$dom = $this->html->load($doc);

			$this->markTestSkipped(
				'Optional p end tags are currently not supported!'
			);

			$this->assertNotNull($dom->find('p', 0), 'Missing p tag');
			$this->assertNotNull($dom->find($t, 0), "Missing {$t} tag");
			$this->assertEquals('PHP Simple HTML DOM Parser', $dom->find('p', 0)->innertext);
		}
	}

	/**
	 * An rt element’s end tag may be omitted if the rt element is immediately
	 * followed by an rt or rp element, or if there is no more content in the
	 * parent element.
	 */
	public function test_optional_rt_end_tag()
	{
		$doc = <<<HTML
<!DOCTYPE HTML>
<html>
<head></head>
<body>
	<div><rt>PHP Simple HTML DOM Parser<rt>A PHP based DOM parser</div>
	<div><rt>PHP Simple HTML DOM Parser<rp>A PHP based DOM parser</rp></div>
</body>
</html>
HTML;

		$dom = $this->html->load($doc);

		$this->assertCount(3, $dom->find('rt'));
		$this->assertEquals('PHP Simple HTML DOM Parser', $dom->find('rt', 0)->innertext);
		$this->assertEquals('A PHP based DOM parser', $dom->find('rt', 1)->innertext);
		$this->assertEquals('PHP Simple HTML DOM Parser', $dom->find('rt', 2)->innertext);
	}

	/**
	 * An rp element’s end tag may be omitted if the rp element is immediately
	 * followed by an rt or rp element, or if there is no more content in the
	 * parent element.
	 */
	public function test_optional_rp_end_tag()
	{
		$doc = <<<HTML
<!DOCTYPE HTML>
<html>
<head></head>
<body>
	<div><rp>PHP Simple HTML DOM Parser<rp>A PHP based DOM parser</div>
	<div><rp>PHP Simple HTML DOM Parser<rt>A PHP based DOM parser</rt></div>
</body>
</html>
HTML;

		$dom = $this->html->load($doc);

		$this->assertCount(3, $dom->find('rp'));
		$this->assertEquals('PHP Simple HTML DOM Parser', $dom->find('rp', 0)->innertext);
		$this->assertEquals('A PHP based DOM parser', $dom->find('rp', 1)->innertext);
		$this->assertEquals('PHP Simple HTML DOM Parser', $dom->find('rp', 2)->innertext);
	}

	/**
	 * An optgroup element’s end tag may be omitted if the optgroup element is
	 * immediately followed by another optgroup element, or if there is no more
	 * content in the parent element.
	 */
	public function test_optional_optgroup_end_tag()
	{
		$doc = <<<HTML
<!DOCTYPE HTML>
<html>
<head></head>
<body><optgroup>PHP Simple HTML DOM Parser<optgroup>A PHP based DOM parser</body>
</html>
HTML;

		$dom = $this->html->load($doc);

		$this->assertCount(2, $dom->find('optgroup'));
		$this->assertEquals('PHP Simple HTML DOM Parser', $dom->find('optgroup', 0)->innertext);
		$this->assertEquals('A PHP based DOM parser', $dom->find('optgroup', 1)->innertext);
	}

	/**
	 * An option element’s end tag may be omitted if the option element is
	 * immediately followed by another option element, or if it is immediately
	 * followed by an optgroup element, or if there is no more content in the
	 * parent element.
	 */
	public function test_optional_option_end_tag()
	{
		$doc = <<<HTML
<!DOCTYPE HTML>
<html>
<head></head>
<body>
	<div><option>PHP Simple HTML DOM Parser<option>A PHP based DOM parser</div>
	<div><option>PHP Simple HTML DOM Parser<optgroup>A PHP based DOM parser</optgroup></div>
</body>
</html>
HTML;

		$dom = $this->html->load($doc);

		$this->assertCount(3, $dom->find('option'));
		$this->assertEquals('PHP Simple HTML DOM Parser', $dom->find('option', 0)->innertext);
		$this->assertEquals('A PHP based DOM parser', $dom->find('option', 1)->innertext);
		$this->assertEquals('PHP Simple HTML DOM Parser', $dom->find('option', 2)->innertext);
	}

	/**
	 * A colgroup element’s start tag may be omitted if the first thing inside
	 * the colgroup element is a col element, and if the element is not
	 * immediately preceded by another colgroup element whose end tag has been
	 * omitted. (It can’t be omitted if the element is empty.)
	 */
	public function test_optional_colgroup_start_tag()
	{
		$doc = <<<HTML
<!DOCTYPE HTML>
<html>
<head></head>
<body>
	<col>PHP Simple HTML DOM Parser</colgroup><col>A PHP based DOM parser</colgroup>
</body>
</html>
HTML;

		$dom = $this->html->load($doc);

		$this->markTestSkipped(
			'Optional start tags are not supported right now'
		);

		$this->assertCount(2, $dom->find('colgroup'));
		$this->assertEquals('PHP Simple HTML DOM Parser', $dom->find('colgroup', 0)->innertext);
		$this->assertEquals('A PHP based DOM parser', $dom->find('colgroup', 1)->innertext);
	}

	/**
	 * A colgroup element’s end tag may be omitted if the colgroup element is
	 * not immediately followed by a space character or a comment.
	 */
	public function test_optional_colgroup_end_tag()
	{
		$doc = <<<HTML
<!DOCTYPE HTML>
<html>
<head></head>
<body><colgroup>PHP Simple HTML DOM Parser</body>
</html>
HTML;

		$dom = $this->html->load($doc);

		$this->assertNotNull($dom->find('colgroup', 0));
		$this->assertEquals('PHP Simple HTML DOM Parser', $dom->find('colgroup', 0)->innertext);
	}

	/**
	 * A caption element’s end tag may be omitted if the caption element is not
	 * immediately followed by a space character or a comment.
	 */
	public function test_optional_caption_end_tag()
	{
		$doc = <<<HTML
<!DOCTYPE HTML>
<html>
<head></head>
<body><caption>PHP Simple HTML DOM Parser</body>
</html>
HTML;

		$dom = $this->html->load($doc);

		$this->assertNotNull($dom->find('caption', 0));
		$this->assertEquals('PHP Simple HTML DOM Parser', $dom->find('caption', 0)->innertext);
	}

	/**
	 * A thead element’s end tag may be omitted if the thead element is
	 * immediately followed by a tbody or tfoot element.
	 */
	public function test_optional_thead_end_tag()
	{
		$token = array('tbody', 'tfoot');

		foreach ($token as $t) {
			$doc = <<<HTML
<!DOCTYPE HTML>
<html>
<head></head>
<body><thead><{$t}>PHP Simple HTML DOM Parser</{$t}></body>
</html>
HTML;

			$dom = $this->html->load($doc);

			$this->assertNotNull($dom->find('thead', 0), 'Missing thead tag');
			$this->assertNotNull($dom->find($t, 0), "Missing {$t} tag");
			$this->assertEquals("<{$t}>PHP Simple HTML DOM Parser</{$t}>", $dom->find('thead', 0)->innertext);
		}
	}

	/**
	 * A tbody element’s start tag may be omitted if the first thing inside the
	 * tbody element is a tr element, and if the element is not immediately
	 * preceded by a tbody, thead, or tfoot element whose end tag has been
	 * omitted. (It can’t be omitted if the element is empty.)
	 */
	public function test_optional_tbody_start_tag()
	{
		$doc = <<<HTML
<!DOCTYPE HTML>
<html>
<head></head>
<body><tr>PHP Simple HTML DOM Parser</tr></tbody></body>
</html>
HTML;

		$dom = $this->html->load($doc);

		$this->markTestSkipped(
			'tbody is currently skipped by the parser'
		);

		$this->assertNotNull($dom->find('tbody', 0));
		$this->assertEquals('PHP Simple HTML DOM Parser', $dom->find('tbody', 0)->innertext);
	}

	/**
	 * A tbody element’s end tag may be omitted if the tbody element is
	 * immediately followed by a tbody or tfoot element, or if there is no more
	 * content in the parent element.
	 */
	public function test_optional_tbody_end_tag()
	{
		$doc = <<<HTML
<!DOCTYPE HTML>
<html>
<head></head>
<body><tbody>PHP Simple HTML DOM Parser<tbody>A PHP based DOM parser</body>
</html>
HTML;

		$dom = $this->html->load($doc);

		$this->markTestSkipped(
			'tbody is currently skipped by the parser'
		);

		$this->assertCount(2, $dom->find('tbody'));
		$this->assertEquals('PHP Simple HTML DOM Parser', $dom->find('tbody', 0)->innertext);
		$this->assertEquals('A PHP based DOM parser', $dom->find('tbody', 1)->innertext);
	}

	/**
	 * A tfoot element’s end tag may be omitted if there is no more content in
	 * the parent element.
	 */
	public function test_optional_tfoot_end_tag()
	{
		$doc = <<<HTML
<!DOCTYPE HTML>
<html>
<head></head>
<body><tfoot>PHP Simple HTML DOM Parser</body>
</html>
HTML;

		$dom = $this->html->load($doc);

		$this->assertNotNull($dom->find('tfoot', 0));
		$this->assertEquals('PHP Simple HTML DOM Parser', $dom->find('tfoot', 0)->innertext);
	}

	/**
	 * A tr element’s end tag may be omitted if the tr element is immediately
	 * followed by another tr element, or if there is no more content in the
	 * parent element.
	 */
	public function test_optional_tr_end_tag()
	{
		$doc = <<<HTML
<!DOCTYPE HTML>
<html>
<head></head>
<body><tr>PHP Simple HTML DOM Parser<tr>A PHP based DOM parser</body>
</html>
HTML;

		$dom = $this->html->load($doc);

		$this->assertCount(2, $dom->find('tr'));
		$this->assertEquals('PHP Simple HTML DOM Parser', $dom->find('tr', 0)->innertext);
		$this->assertEquals('A PHP based DOM parser', $dom->find('tr', 1)->innertext);
	}

	/**
	 * A td element’s end tag may be omitted if the td element is immediately
	 * followed by a td or th element, or if there is no more content in the
	 * parent element.
	 */
	public function test_optional_td_end_tag()
	{
		$doc = <<<HTML
<!DOCTYPE HTML>
<html>
<head></head>
<body>
	<div><td>PHP Simple HTML DOM Parser<td>A PHP based DOM parser</div>
	<div><td>PHP Simple HTML DOM Parser<th>A PHP based DOM parser</th></div>
</body>
</html>
HTML;

		$dom = $this->html->load($doc);

		$this->assertCount(3, $dom->find('td'));
		$this->assertEquals('PHP Simple HTML DOM Parser', $dom->find('td', 0)->innertext);
		$this->assertEquals('A PHP based DOM parser', $dom->find('td', 1)->innertext);
		$this->assertEquals('PHP Simple HTML DOM Parser', $dom->find('td', 2)->innertext);
	}

	/**
	 * A th element’s end tag may be omitted if the th element is immediately
	 * followed by a td or th element, or if there is no more content in the
	 * parent element.
	 */
	public function test_optional_th_end_tag()
	{
		$doc = <<<HTML
<!DOCTYPE HTML>
<html>
<head></head>
<body>
	<div><th>PHP Simple HTML DOM Parser<th>A PHP based DOM parser</div>
	<div><th>PHP Simple HTML DOM Parser<td>A PHP based DOM parser</td></div>
</body>
</html>
HTML;

		$dom = $this->html->load($doc);

		$this->assertCount(3, $dom->find('th'));
		$this->assertEquals('PHP Simple HTML DOM Parser', $dom->find('th', 0)->innertext);
		$this->assertEquals('A PHP based DOM parser', $dom->find('th', 1)->innertext);
		$this->assertEquals('PHP Simple HTML DOM Parser', $dom->find('th', 2)->innertext);
	}

	/**
	 * Checks if optional end tags are properly handled by the parser.
	 *
	 * @dataProvider dataProvider_for_parser_should_close_optional_end_tags
	 */
	public function test_parser_should_close_optional_end_tags($doc, $expected, $message)
	{
		$this->html->load($doc);
		$this->assertEquals($expected, $this->html->save(), $message);
	}

	/**
	 * @todo: The list of block tags and optional closing tags should come from
	 * code, not copied here.
	 */
	public function dataProvider_for_parser_should_close_optional_end_tags()
	{
		$block_tags = array(
			'body' => 1,
			'div' => 1,
			'form' => 1,
			'root' => 1,
			'span' => 1,
			'table' => 1
		);

		// Remove root (implicitly added by the parser)
		unset($block_tags['root']);

		$optional_closing_tags = array(
			// Not optional, see
			// https://www.w3.org/TR/html/textlevel-semantics.html#the-b-element
			'b' => array('b' => 1),
			'dd' => array('dd' => 1, 'dt' => 1),
			// Not optional, see
			// https://www.w3.org/TR/html/grouping-content.html#the-dl-element
			'dl' => array('dd' => 1, 'dt' => 1),
			'dt' => array('dd' => 1, 'dt' => 1),
			'li' => array('li' => 1),
			'optgroup' => array('optgroup' => 1, 'option' => 1),
			'option' => array('optgroup' => 1, 'option' => 1),
			'p' => array('p' => 1),
			'rp' => array('rp' => 1, 'rt' => 1),
			'rt' => array('rp' => 1, 'rt' => 1),
			'td' => array('td' => 1, 'th' => 1),
			'th' => array('td' => 1, 'th' => 1),
			'tr' => array('td' => 1, 'th' => 1, 'tr' => 1),
		);

		$data = array();

		// Block tags should close optional elements
		foreach(array_keys($block_tags) as $block) {
			foreach(array_keys($optional_closing_tags) as $e) {
				$data["$block should close $e"] = array(
					"<$block><$e></$block>",
					"<$block><$e></$e></$block>",
					"$block should close $e"
				);
			}
		}

		// Special case for root (has no tags)
		foreach(array_keys($optional_closing_tags) as $e) {
			$data["root should close $e"] = array(
				"<$e>",
				"<$e></$e>",
				"root should close $e"
			);
		}

		// Block tags should close optional elements, even if the opening tag is missing
		foreach(array_keys($block_tags) as $block) {
			foreach(array_keys($optional_closing_tags) as $e) {
				$data["$block should close $e"] = array(
					"<$e></$block>",
					"<$e></$e></$block>",
					"$block should close $e"
				);
			}
		}

		// Block tags should close NESTED optional elements
		foreach(array_keys($block_tags) as $block) {
			foreach(array_keys($optional_closing_tags) as $e) {
				foreach(array_keys($optional_closing_tags[$e]) as $child) {

					// skip if element closes itself
					if($e === $child) continue;

					// skip if child and element are mutual exclusive
					if(isset($optional_closing_tags[$child])
					&& array_key_exists($e, $optional_closing_tags[$child])) {
						continue;
					}

					$data["$block should close nested $e and $child"] = array(
						"<$block><$e><$child></$block>",
						"<$block><$e><$child></$child></$e></$block>",
						"$block should close nested $e and $child"
					);
				}
			}
		}

		// Special case for root (has not tags)
		foreach(array_keys($block_tags) as $block) {
			foreach(array_keys($optional_closing_tags) as $e) {
				foreach(array_keys($optional_closing_tags[$e]) as $child) {

					// skip if nested element closes itself
					if($e === $child) continue;

					// skip if child and element are mutual exclusive
					if(isset($optional_closing_tags[$child])
					&& array_key_exists($e, $optional_closing_tags[$child])) {
						continue;
					}

					$data["root should close nested $e and $child"] = array(
						"<$e><$child>",
						"<$e><$child></$child></$e>",
						"root should close nested $e and $child"
					);
				}
			}
		}

		// Some optional tags should close other optional tags
		foreach(array_keys($optional_closing_tags) as $e) {
			foreach(array_keys($optional_closing_tags[$e]) as $child) {
				$data["$e should close $child"] = array(
					"<$child><$e>",
					"<$child></$child><$e></$e>",
					"$e should close $child"
				);
			}
		}

		// Optional tags should NOT close stray elements
		foreach(array_keys($optional_closing_tags) as $e) {
			$data["$e should NOT close a"] = array(
				"<a><$e>",
				"<a><$e></$e>",
				"$e should NOT close a"
			);
		}

		// Normal tags should NOT close optional elements
		foreach(array_keys($optional_closing_tags) as $e) {
			$data["a should NOT close $e"] = array(
				"<$e><a></a>",
				"<$e><a></a></$e>",
				"a should NOT close $e"
			);
		}

		return $data;
	}
}

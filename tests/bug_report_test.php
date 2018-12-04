<?php
require_once __DIR__ . '/../simple_html_dom.php';
use PHPUnit\Framework\TestCase;

/**
 * Tests for bugs reported in the bug tracker
 *
 * @link https://sourceforge.net/p/simplehtmldom/bugs
 * Bug tracker
 */
class bug_report_test extends TestCase {
	private $html;

	protected function setUp() {
		$this->html = new simple_html_dom;
	}

	/**
	 * Bug #154 (Fatal error: Call to a member function find() on null)
	 *
	 * The parser incorrectly removes everything between `{` and `}` attempting
	 * to remove "Smarty Scripts" from the DOM. This causes regular text to be
	 * removed as well, if it contains curly braces.
	 *
	 * **Example Code**:
	 *
	 * ```HTML
	 * <div class="before"></div>
	 *   <p>{PHP Simple HTML DOM Parser</p>
	 *   <p>{A PHP based DOM parser}</p>
	 * <div id="after"></div>
	 * ```
	 *
	 * @link https://sourceforge.net/p/simplehtmldom/bugs/154/ Bug #154
	 * @link https://www.smarty.net Smarty
	 */
	public function test_bug_154() {
		$doc = <<<HTML
<p>{PHP Simple HTML DOM Parser</p>
<p>{A PHP based DOM parser}</p>
HTML;

		$this->html->load($doc);

		$this->assertCount(2, $this->html->find('p'));

		$this->assertEquals(
			'{PHP Simple HTML DOM Parser',
			$this->html->find('p', 0)->innertext
		);

		$this->assertEquals(
			'{A PHP based DOM parser}',
			$this->html->find('p', 1)->innertext
		);
	}

	/**
	 * Bug #160 (Parsing fails with '<-' + '/' symbols combination in string)
	 *
	 * **Example Code**:
	 *
	 * ```HTML
	 * <div id="before"></div>
	 *   <span>---> PHP Simple HTML DOM Parser <--- A /PHP based DOM parser</span>
	 * <div id="after"></div>
	 * ```
	 *
	 * **Note**:
	 *
	 * The [Markup Validation Service](https://validator.w3.org/#validate_by_input)
	 * reports: Bad character `-` after `<`. Probable cause: Unescaped `<`. Try
	 * escaping it as `&lt;`.
	 *
	 * @link https://sourceforge.net/p/simplehtmldom/bugs/160/ Bug #160
	 * @link https://validator.w3.org/#validate_by_input Markup Validation Service
	 */
	public function test_bug_160() {
		$doc = <<<HTML
<div id="before"></div>
	<span>---> PHP Simple HTML DOM Parser <--- A /PHP based DOM parser</span>
<div id="after"></div>
HTML;

		$this->html->load($doc);

		$this->assertEquals(
			'---> PHP Simple HTML DOM Parser <--- A /PHP based DOM parser',
			$this->html->find('span', 0)->innertext
		);
	}

}
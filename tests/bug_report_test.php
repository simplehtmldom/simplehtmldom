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
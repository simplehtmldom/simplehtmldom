<?php
require_once __DIR__ . '/../simple_html_dom.php';
use PHPUnit\Framework\TestCase;

/**
 * Tests the attribute parsing behavior of the parser
 */
class attribute_test extends TestCase {
	private $html;

	protected function setUp()
	{
		$this->html = new simple_html_dom();
	}

	protected function tearDown()
	{
		$this->html->clear();
		unset($this->html);
	}

	/** @dataProvider dataProvider_for_attribute_should_parse */
	public function test_attribute_should_parse($expected, $doc)
	{
		$this->html->load($doc);
		$this->assertEquals($expected, $this->html->save());
	}

	public function dataProvider_for_attribute_should_parse()
	{
		return array(
			'double quotes' => array(
				'<p class="hidden"></p>',
				'<p class="hidden"></p>'
			),
			'single quotes' => array(
				'<p class=\'hidden\'></p>',
				'<p class=\'hidden\'></p>'
			),
			'no quotes' => array(
				'<p class=hidden></p>',
				'<p class=hidden></p>'
			),
			'no value' => array(
				'<p hidden></p>',
				'<p hidden></p>'
			)
		);
	}
}

<?php
require_once __DIR__ . '/../simple_html_dom.php';
use PHPUnit\Framework\TestCase;

/**
 * Checks if the parser properly handles DOCTYPE
 */
class doctype_test extends TestCase {

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

	/**
	 * @dataProvider dataProvider_for_doctype_should_parse
	 */
	public function test_doctype_should_parse($expected, $doc)
	{
		// Note: The parser currently doesn't make any assumptions about DOCTYPE
		$this->html->load($doc);
		$this->assertEquals($expected, $this->html->root->plaintext);
		$this->assertEquals($doc, $this->html->save());
	}

	public function dataProvider_for_doctype_should_parse()
	{
		return array(
			'normal' => array(
				'',
				'<!DOCTYPE html><html></html>',
			),
			'stray doctype' => array(
				'Hello, World!',
				'<p><!DOCTYPE html>Hello, World!</p>',
			),
		);
	}
}

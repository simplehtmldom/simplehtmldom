<?php
require_once __DIR__ . '/../simple_html_dom.php';
use PHPUnit\Framework\TestCase;

/**
 * Checks if the parser properly handles style elements
 */
class style_test extends TestCase {

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
	 * @dataProvider dataProvider_for_style_should_parse
	 */
	public function test_style_should_parse($expected, $doc)
	{
		$this->html->load($doc);
		$this->assertEquals($expected, $this->html->find('style', 0)->innertext);
		$this->assertEquals($doc, $this->html->save());
	}

	public function dataProvider_for_style_should_parse()
	{
		return array(
			'empty' => array(
				'',
				'<style></style>',
			),
			'empty without end tag' => array(
				'',
				'<style/>',
			),
			'space' => array(
				' ',
				'<style> </style>',
			),
			'newline' => array(
				"\n",
				"<style>\n</style>"
			),
			'multiple style tags' => array(
				'Hello',
				'<style>Hello</style><style>World</style>'
			),
		);
	}
}

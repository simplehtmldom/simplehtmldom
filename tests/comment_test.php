<?php
require_once __DIR__ . '/../simple_html_dom.php';
use PHPUnit\Framework\TestCase;

/**
 * Checks if the parser properly handles comments
 */
class comment_test extends TestCase {

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
	 * @dataProvider dataProvider_for_comment_should_parse
	 */
	public function test_comment_should_parse($expected, $doc)
	{
		$this->html->load($doc);
		$this->assertEquals($expected, $this->html->find('comment', 0)->innertext);
		$this->assertEquals($doc, $this->html->save());
	}

	public function dataProvider_for_comment_should_parse()
	{
		return array(
			'empty' => array(
				'',
				'<!---->',
			),
			'space' => array(
				' ',
				'<!-- -->',
			),
			'brackets' => array(
				']][[',
				'<!--]][[-->',
			),
			'html' => array(
				'<p>Hello, World!</p>',
				'<!--<p>Hello, World!</p>-->',
			),
			'cdata' => array(
				'<![CDATA[Hello, World!]]>',
				'<!--<![CDATA[Hello, World!]]>-->'
			),
			'newline' => array(
				"Hello\nWorld!",
				"<!--Hello\nWorld!-->"
			),
		);
	}

	public function test_html_inside_comment_should_not_appear_in_the_dom()
	{
		$this->html->load('<!-- <div>Hello, World!</div> -->');
		$this->assertNotNull($this->html->find('comment', 0));
		$this->assertNull($this->html->find('div', 0));
	}
}

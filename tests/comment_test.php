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
			'whitespace' => array(
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
		);
	}
}

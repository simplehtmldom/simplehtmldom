<?php
require_once __DIR__ . '/../simple_html_dom.php';
use PHPUnit\Framework\TestCase;

/**
 * Checks if the parser properly handles CDATA sections
 */
class cdata_test extends TestCase {

	private $html;

	protected function setUp()
	{
		$this->html = new simple_html_dom;
	}

	/**
	 * @dataProvider dataProvider_for_cdata_should_parse
	 */
	public function test_cdata_should_parse($expected, $doc)
	{
		$this->html->load($doc);
		$this->assertEquals($expected, $this->html->find('cdata', 0)->innertext);
		$this->assertEquals($doc, $this->html->save());
	}

	public function dataProvider_for_cdata_should_parse()
	{
		return array(
			'empty' => array(
				'',
				'<![CDATA[]]>',
			),
			'whitespace' => array(
				' ',
				'<![CDATA[ ]]>',
			),
			'brackets' => array(
				']][[',
				'<![CDATA[]][[]]>',
			),
			'html' => array(
				'<p>Hello, World!</p>',
				'<![CDATA[<p>Hello, World!</p>]]>',
			),
			'comment' => array(
				'<!-- Hello, World! -->',
				'<![CDATA[<!-- Hello, World! -->]]>'
			),
		);
	}
}

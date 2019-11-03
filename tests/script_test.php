<?php
require_once __DIR__ . '/../simple_html_dom.php';
use PHPUnit\Framework\TestCase;

/**
 * Checks if the parser properly handles script elements
 */
class script_test extends TestCase {

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
	 * @dataProvider dataProvider_for_script_should_parse
	 */
	public function test_script_should_parse($expected, $doc)
	{
		$this->html->load($doc);
		$this->assertEquals($expected, $this->html->find('script', 0)->innertext);
		$this->assertEquals($doc, $this->html->save());
	}

	public function dataProvider_for_script_should_parse()
	{
		return array(
			'empty' => array(
				'',
				'<script></script>',
			),
			'empty with type' => array(
				'',
				'<script type="application/javascript"></script>',
			),
			'space' => array(
				' ',
				'<script> </script>',
			),
			'html string' => array(
				"var foo = '<div>Hello, World!</div>';",
				"<script>var foo = '<div>Hello, World!</div>';</script>",
			),
			'newline' => array(
				"\n",
				"<script>\n</script>"
			),
			'newline with type' => array(
				"\n",
				"<script type=\"application/javascript\">\n</script>"
			),
		);
	}

	public function test_html_inside_script_should_not_appear_in_the_dom()
	{
		$this->html->load('<script><div>Hello, World!</div></script>');
		$this->assertNotNull($this->html->find('script', 0));
		$this->assertNull($this->html->find('div', 0));
	}
}

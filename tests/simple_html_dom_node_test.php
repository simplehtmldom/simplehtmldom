<?php
require_once __DIR__ . '/../simple_html_dom.php';
use PHPUnit\Framework\TestCase;

/**
 * Tests for simple_html_dom_node
 */
class simple_html_dom_node_test extends TestCase {

	private $html;

	protected function setUp()
	{
		$this->html = new simple_html_dom;
	}

	public function test_removeChild()
	{
		$expected = <<<EOD
<html>
<body>

</body>
</html>
EOD;

		$doc = <<<EOD
<html>
<body>
<table>
	<tr><th>Title</th></tr>
	<tr><td>Row 1</td></tr>
</table>
</body>
</html>
EOD;

		$this->html = str_get_html(
			$doc,
			true,
			true,
			DEFAULT_TARGET_CHARSET,
			false,
			DEFAULT_BR_TEXT,
			DEFAULT_SPAN_TEXT
		);

		$body = $this->html->find('body', 0);

		$this->assertEquals($doc, $this->html->save());

		$body->removeChild($body->find('table', 0));

		$this->assertEquals($expected, $this->html->save());
	}

	public function test_hasClass_should_return_true()
	{
		$this->html = str_get_html('<p class="article">Simple HTML DOM Parser</p>');

		$this->assertTrue($this->html->find('p', 0)->hasClass('article'));
	}

	public function test_hasClass_should_return_false()
	{
		$this->html = str_get_html('<p>Simple HTML DOM Parser</p>');

		$this->assertFalse($this->html->find('p', 0)->hasClass('article'));
	}

	public function test_addClass_should_add_class()
	{
		$expected = '<p class="article">Simple HTML DOM Parser</p>';

		$this->html = str_get_html('<p>Simple HTML DOM Parser</p>');
		$this->html->find('p', 0)->addClass('article');

		$this->assertEquals($expected, $this->html->save());
	}

	public function test_addClass_should_add_class_list()
	{
		$expected = '<p class="article new">Simple HTML DOM Parser</p>';

		$this->html = str_get_html('<p>Simple HTML DOM Parser</p>');
		$this->html->find('p', 0)->addClass('article new');

		$this->assertEquals($expected, $this->html->save());
	}

	public function test_addClass_should_add_class_array()
	{
		$expected = '<p class="article new">Simple HTML DOM Parser</p>';

		$this->html = str_get_html('<p>Simple HTML DOM Parser</p>');
		$this->html->find('p', 0)->addClass(array('article', 'new'));

		$this->assertEquals($expected, $this->html->save());
	}

	public function test_addClass_should_add_missing_classes()
	{
		$expected = '<p class="article new">Simple HTML DOM Parser</p>';

		$this->html = str_get_html('<p class="article">Simple HTML DOM Parser</p>');
		$this->html->find('p', 0)->addClass('article new');

		$this->assertEquals($expected, $this->html->save());
	}

	public function test_removeClass_should_remove_all_classes()
	{
		$expected = '<p>Simple HTML DOM Parser</p>';

		$this->html = str_get_html('<p class="article">Simple HTML DOM Parser</p>');
		$this->html->find('p', 0)->removeClass();

		$this->assertEquals($expected, $this->html->save());
	}

	public function test_removeClass_should_remove_class_attribute()
	{
		$expected = '<p>Simple HTML DOM Parser</p>';

		$this->html = str_get_html('<p class="article">Simple HTML DOM Parser</p>');
		$this->html->find('p', 0)->removeClass('article');

		$this->assertEquals($expected, $this->html->save());
	}

	public function test_removeClass_should_remove_class()
	{
		$expected = '<p class="article">Simple HTML DOM Parser</p>';

		$this->html = str_get_html('<p class="article new">Simple HTML DOM Parser</p>');
		$this->html->find('p', 0)->removeClass('new');

		$this->assertEquals($expected, $this->html->save());
	}

	public function test_removeClass_should_remove_class_list()
	{
		$expected = '<p>Simple HTML DOM Parser</p>';

		$this->html = str_get_html('<p class="article new">Simple HTML DOM Parser</p>');
		$this->html->find('p', 0)->removeClass('article new');

		$this->assertEquals($expected, $this->html->save());
	}

	public function test_removeClass_should_remove_class_array()
	{
		$expected = '<p>Simple HTML DOM Parser</p>';

		$this->html = str_get_html('<p class="article new">Simple HTML DOM Parser</p>');
		$this->html->find('p', 0)->removeClass(array('article', 'new'));

		$this->assertEquals($expected, $this->html->save());
	}

	public function test_save_should_return_outerhtml()
	{
		$expected = '<p>Simple HTML DOM Parser</p>';

		$this->html = str_get_html('<div><p>Simple HTML DOM Parser</p></div>');

		$this->assertEquals($expected, $this->html->find('p', 0)->save());
	}

	public function test_save_should_create_file()
	{
		$expected_file = __DIR__ . '/data/simple_html_dom_node/save_file_expected.html';
		$file = __DIR__ . '/data/simple_html_dom_node/save_file.html';

		$this->html = str_get_html('<div><p>Simple HTML DOM Parser</p></div>');
		$this->html->find('p', 0)->save($file);

		$this->assertFileExists($file);
		$this->assertFileEquals($expected_file, $file);
	}

}

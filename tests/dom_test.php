<?php
require_once __DIR__ . '/../simple_html_dom.php';
use PHPUnit\Framework\TestCase;

/**
 * Tests for general DOM manipulation
 */
class dom_test extends TestCase {

	private $html;

	protected function setUp()
	{
		$this->html = new simple_html_dom;
	}

	public function test_node_removeChild()
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

}

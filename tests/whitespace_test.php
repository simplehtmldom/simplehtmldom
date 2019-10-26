<?php
require_once __DIR__ . '/../simple_html_dom.php';
use PHPUnit\Framework\TestCase;

/**
 * Test if the parser properly removes whitespace
 */
class whitespace_test extends TestCase {

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

	public function provide_whitespace_around_attributes()
	{
		return array(array(<<<EOD
<html>
<head>
	<meta charset="UTF-8">
	<meta name ="description" content= "simplehtmldom">
	<meta name = "keywords" content   =   "simple,html,dom">
	<meta      name      =        "author"
		content          =        "John Doe">
</head>
</html>
EOD
));
	}

	public function provide_whitespace_around_void_tags()
	{
		return array(array(<<<EOD
<html>
<head>
	<meta charset="UTF-8">
	< meta name="description" content="simplehtmldom">
	<meta name="keywords" content="simple,html,dom" >
	< meta name="author" content="John Doe" >
	<       meta name="viewport" content="width=device-width, initial-scale=1.0"
	>
</head>
</html>
EOD
));
	}

	public function provide_whitespace_around_tags()
	{
		return array(array(<<<EOD
<html>
<body>
	<div class="article" />
	< div class="article" />
	<div class="article" / >
	< div class="article" / >
	<
	div class="article" /
	>
	<     div class="article" /     >
</body>
</html>
EOD
));
	}

	public function provide_whitespace_around_tags_without_class()
	{
		return array(array(<<<EOD
<html>
<body>
	<div />
	< div />
	<div / >
	< div / >
	<
	div /
	>
	<     div /     >
</body>
</html>
EOD
));
	}

	public function provide_whitespace_around_nested_tags()
	{
		return array(array(<<<EOD
<html>
<body>
	<div class="article">
	< div class="level1">
	<div class="level2" >
	< div class="level3" >
	<
	div class="level4"
	>
	<     div class="level5"     >
	<     /div     >
	<
	/div
	>
	< /div >
	</div >
	< /div>
	</ div>
</body>
</html>
EOD
));
	}

	public function provide_whitespace_in_class_values()
	{
		return array(array(<<<EOD
<html>
<body>
	<div class=""/>
	<div class=" "/>
	<div class="     "/>
	<div class="article"/>
	<div class=" article"/>
	<div class="article "/>
	<div class=" article "/>
	<div class="     article     "/>
	<div class="article new"/>
	<div class=" article new"/>
	<div class="article new "/>
	<div class="article     new"/>
	<div class="     article     new     "/>
	<div class="
	article
	new
	"/>
</body>
</html>
EOD
));
	}

	public function provide_whitespace_in_attribute_values()
	{
		return array(array(<<<EOD
<html>
<body>
	<div attribute=""/>
	<div attribute=" "/>
	<div attribute="     "/>
	<div attribute="article"/>
	<div attribute=" article"/>
	<div attribute="article "/>
	<div attribute=" article "/>
	<div attribute="     article     "/>
	<div attribute="article new"/>
	<div attribute=" article new"/>
	<div attribute="article new "/>
	<div attribute="article     new"/>
	<div attribute="     article     new     "/>
	<div attribute="
	article
	new
	"/>
</body>
</html>
EOD
));
	}

	/** @dataProvider provide_whitespace_around_attributes */
	public function test_parse_removes_whitespace_around_attributes($doc)
	{
		// phpcs:ignore Generic.Files.LineLength
		$expected = '<html><head><meta charset="UTF-8"><meta name="description" content="simplehtmldom"><meta name="keywords" content="simple,html,dom"><meta name="author" content="John Doe"></head></html>';
		$this->html->load($doc);

		$this->assertEquals($expected, $this->html->save());
	}

	/** @dataProvider provide_whitespace_around_void_tags */
	public function test_parse_removes_whitespace_around_void_tags($doc)
	{
		// phpcs:ignore Generic.Files.LineLength
		$expected = '<html><head><meta charset="UTF-8"><meta name="description" content="simplehtmldom"><meta name="keywords" content="simple,html,dom"><meta name="author" content="John Doe"><meta name="viewport" content="width=device-width, initial-scale=1.0"></head></html>';
		$this->html->load($doc);

		$this->assertEquals($expected, $this->html->save());
	}

	/** @dataProvider provide_whitespace_around_tags */
	public function test_parse_removes_whitespace_around_tags($doc)
	{
		// phpcs:ignore Generic.Files.LineLength
		$expected = '<html><body><div class="article"/><div class="article"/><div class="article"/><div class="article"/><div class="article"/><div class="article"/></body></html>';
		$this->html->load($doc);

		$this->assertEquals($expected, $this->html->save());
	}

	/** @dataProvider provide_whitespace_around_tags_without_class */
	public function test_parse_removes_whitespace_around_tags_without_class($doc)
	{
		// phpcs:ignore Generic.Files.LineLength
		$expected = '<html><body><div/><div/><div/><div/><div/><div/></body></html>';
		$this->html->load($doc);

		$this->assertEquals($expected, $this->html->save());
	}

	/** @dataProvider provide_whitespace_around_nested_tags */
	public function test_parse_removes_whitespace_around_nested_tags($doc)
	{
		// phpcs:ignore Generic.Files.LineLength
		$expected = '<html><body><div class="article"><div class="level1"><div class="level2"><div class="level3"><div class="level4"><div class="level5"></div></div></div></div></div></div></body></html>';
		$this->html->load($doc);

		$this->assertEquals($expected, $this->html->save());
	}

	/** @dataProvider provide_whitespace_in_class_values */
	public function test_parse_removes_whitespace_in_class_values($doc)
	{
		$this->html->load($doc);

		$this->assertCount(11, $this->html->find('.article'));
		$this->assertCount(6, $this->html->find('.new'));
		$this->assertCount(6, $this->html->find('[class="article new"]'));
		$this->assertEquals('article', $this->html->find('.article', 0)->class);
		$this->assertEquals('article new', $this->html->find('[class="article new"]', 0)->class);
	}

	/** @dataProvider provide_whitespace_in_class_values */
	public function test_find_removes_whitespace_in_class_selectors($doc)
	{
		$this->html->load($doc);

		$this->assertCount(11, $this->html->find('.article'));
		$this->assertCount(11, $this->html->find(' .article'));
		$this->assertCount(11, $this->html->find('.article '));
		$this->assertCount(11, $this->html->find(' .article '));
		$this->assertCount(11, $this->html->find('     .article     '));

		$this->assertCount(6, $this->html->find('[class="article new"]' ));
		$this->assertCount(6, $this->html->find('[class=" article new"]' ));
		$this->assertCount(6, $this->html->find('[class="article new "]' ));
		$this->assertCount(6, $this->html->find('[class=" article new "]' ));
		$this->assertCount(6, $this->html->find('[class="article     new"]' ));
		$this->assertCount(6, $this->html->find('[class="     article     new     "]' ));
	}

	/** @dataProvider provide_whitespace_in_attribute_values */
	public function test_parse_removes_whitespace_in_attribute_values($doc)
	{
		$this->html->load($doc);

		$this->assertCount(11, $this->html->find('[attribute*="article"]'));
		$this->assertCount(6, $this->html->find('[attribute*="new"]'));
		$this->assertCount(6, $this->html->find('[attribute="article new"]'));
		$this->assertEquals('article', $this->html->find('[attribute*="article"]', 0)->attribute);
		$this->assertEquals('article new', $this->html->find('[attribute*="article new"]', 0)->attribute);
	}

	/** @dataProvider provide_whitespace_in_class_values */
	public function test_find_keeps_whitespace_without_trim($doc)
	{
		$this->html->load($doc, true, false);

		$this->assertCount(11, $this->html->find('.article'));
		$this->assertCount(11, $this->html->find(' .article'));
		$this->assertCount(11, $this->html->find('.article '));
		$this->assertCount(11, $this->html->find(' .article '));
		$this->assertCount(11, $this->html->find('     .article     '));

		$this->assertCount(6, $this->html->find('[class="article new"]' ));
		$this->assertCount(6, $this->html->find('[class=" article new"]' ));
		$this->assertCount(6, $this->html->find('[class="article new "]' ));
		$this->assertCount(6, $this->html->find('[class=" article new "]' ));
		$this->assertCount(6, $this->html->find('[class="article     new"]' ));
		$this->assertCount(6, $this->html->find('[class="     article     new     "]' ));
	}

	/**
	 * @dataProvider provide_whitespace_around_attributes
	 * @dataProvider provide_whitespace_around_void_tags
	 * @dataProvider provide_whitespace_around_tags
	 * @dataProvider provide_whitespace_around_tags_without_class
	 * @dataProvider provide_whitespace_around_nested_tags
	 * @dataProvider provide_whitespace_in_attribute_values
	 * @dataProvider provide_whitespace_in_class_values
	 */
	public function test_parse_keeps_whitespace_without_trim($doc)
	{
		$expected = $doc;
		$this->html->load($doc, true, false);

		$this->assertEquals($expected, $this->html->save());
	}

	public function test_text_should_trim_whitespace()
	{
		$expected = 'PHP Simple HTML DOM Parser';

		$doc = <<<EOD
<p>     PHP Simple HTML DOM Parser     </p>
EOD;

		$this->html->load($doc);

		$this->assertEquals($expected, $this->html->root->text());
	}

	public function test_text_should_trim_nested_whitespace()
	{
		$expected = 'PHP Simple HTML DOM Parser';

		$doc = <<<EOD
<p>   <span>     </span>  PHP Simple HTML DOM Parser     </p>
EOD;

		$this->html->load($doc);

		$this->assertEquals($expected, $this->html->root->text());
	}

	public function test_text_should_remove_newline_from_paragraph()
	{
		$expected = 'PHP Simple HTML DOM Parser';

		$doc = <<<EOD
<p>
PHP Simple HTML DOM Parser
</p>
EOD;

		$this->html->load($doc);

		$this->assertEquals($expected, $this->html->root->text());
	}

	public function test_text_should_remove_nested_newline_from_paragraph()
	{
		$expected = 'PHP Simple HTML DOM Parser';

		$doc = <<<EOD
<p>
<span>

</span>
PHP Simple HTML DOM Parser
</p>
EOD;

		$this->html->load($doc);

		$this->assertEquals($expected, $this->html->root->text());
	}

	public function test_text_should_add_newline_between_paragraph()
	{
		$expected = <<<EOD
PHP Simple HTML DOM Parser

A fast, simple and reliable HTML document parser for PHP.
EOD;

		$doc = <<<EOD
<p>PHP Simple HTML DOM Parser</p>
<p>A fast, simple and reliable HTML document parser for PHP.</p>
EOD;

		$this->html->load($doc);

		$this->assertEquals($expected, $this->html->root->text());
	}

	public function test_text_should_add_newline_between_nested_paragraph()
	{
		$expected = <<<EOD
PHP Simple HTML DOM Parser

A fast, simple and reliable HTML document parser for PHP.
EOD;

		$doc = <<<EOD
<div><p>PHP Simple HTML DOM Parser</p></div>
<div><p>A fast, simple and reliable HTML document parser for PHP.</p></div>
EOD;

		$this->html->load($doc);

		$this->assertEquals($expected, $this->html->root->text());
	}

	public function test_text_should_keep_whitespace_around_inline_elements()
	{
		$expected = <<<EOD
PHP Simple HTML DOM Parser. A fast, simple and reliable HTML document parser for PHP.
EOD;

		$doc = <<<EOD
<p>PHP Simple HTML DOM Parser. <em>A fast, simple and reliable HTML document parser for PHP.</em></p>
EOD;

		$this->html->load($doc);

		$this->assertEquals($expected, $this->html->root->text());
	}

	public function test_text_should_skip_empty_paragraphs()
	{
		$expected = <<<EOD
PHP Simple HTML DOM Parser.

A fast, simple and reliable HTML document parser for PHP.
EOD;

		$doc = <<<EOD
<p>PHP Simple HTML DOM Parser.</p>
<p> </p>
<p> </p>
<p> </p>
<p> </p>
<p>A fast, simple and reliable HTML document parser for PHP.</p>
EOD;

		$this->html->load($doc);

		$this->assertEquals($expected, $this->html->root->text());
	}

	public function test_text_should_handle_nbsp_like_whitespace()
	{
		$expected = <<<EOD
PHP Simple HTML DOM Parser.

A fast, simple and reliable HTML document parser for PHP.
EOD;

		$doc = <<<EOD
<p>&nbsp;PHP Simple HTML DOM Parser.&nbsp;</p>&nbsp;
<p>&nbsp;</p>&nbsp;
<p>&nbsp;</p>&nbsp;
<p>&nbsp;</p>&nbsp;
<p>&nbsp;</p>&nbsp;
<p>&nbsp;A fast, simple and reliable HTML<span>&nbsp;&nbsp;</span>&nbsp; document parser for PHP.&nbsp;</p>
EOD;

		$this->html->load($doc);

		$this->assertEquals($expected, $this->html->root->text());
	}

	/**
	 * The library uses UTF-8 internally. All operations in the text() function
	 * should therefore handle UTF-8 characters accordingly. If UTF-8 characters
	 * are handled like ASCII, the resulting output could be incorrect or cause
	 * errors.
	 *
	 * @link https://sourceforge.net/p/simplehtmldom/feature-requests/62/ Feature #62
	 */
	public function test_text_should_handle_utf8_characters()
	{
		$expected = '«Hello, World»';

		$doc = '&#xAB;Hello, World&#xBB;&nbsp;';

		$this->html->load($doc);

		$this->assertEquals(
			$expected,
			$this->html->root->text(),
			'UTF-8 characters should not be handled like ASCII characters!'
		);
	}

}

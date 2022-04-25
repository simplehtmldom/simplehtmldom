<?php
require_once __DIR__ . '/../simple_html_dom.php';
use PHPUnit\Framework\TestCase;
use simplehtmldom\HtmlElement;

/**
 * Tests for simple_html_dom_node
 */
class htmlnode_test extends TestCase {

	private $html;

	public function rawTextElementProvider()
	{
		return [
			HtmlElement::SCRIPT => [HtmlElement::SCRIPT],
			HtmlElement::STYLE => [HtmlElement::STYLE]
		];
	}

	protected function setUp()
	{
		$this->html = new simple_html_dom;
	}

	protected function tearDown()
	{
		$this->html->clear();
		unset($this->html);
	}

	public function test___get_should_return_false_for_unknown_attributes()
	{
		$doc = '<html></html>';
		$this->html->load($doc);

		$this->assertFalse($this->html->find('html', 0)->lang);
	}

	public function test___set_should_overwrite_innertext()
	{
		$expected = 'PHP Simple HTML DOM Parser';
		$doc = '<html><!--Hello, World!--></html>';
		$this->html->load($doc);
		$this->html->find('comment', 0)->innertext = '';
		$this->html->find('html', 0)->innertext = $expected;

		$this->assertEquals($expected, $this->html->find('html', 0)->innertext());
		$this->assertEmpty($this->html->find('comment', 0)->innertext());
	}

	public function test___unset_should_remove_attribute()
	{
		$doc = '<html lang="en"></html>';
		$this->html->load($doc);
		unset($this->html->find('html', 0)->lang);

		$this->assertFalse($this->html->find('html', 0)->lang);
	}

	public function test_remove_should_remove_node()
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

		$this->html->load($doc, true, false);

		$table = $this->html->find('table', 0);

		$this->assertEquals($doc, $this->html->save());

		$table->remove();

		$this->assertEquals($expected, $this->html->save());
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

		$this->html->load($doc, true, false);

		$body = $this->html->find('body', 0);

		$this->assertEquals($doc, $this->html->save());

		$body->removeChild($body->find('table', 0));

		$this->assertEquals($expected, $this->html->save());
	}

	public function test_has_child_should_return_boolean()
	{
		$doc = '<html><p>PHP Simple HTML DOM Parser</p></html>';
		$this->html->load($doc);

		$this->assertTrue($this->html->find('html', 0)->has_child());
		$this->assertFalse($this->html->find('p', 0)->has_child());

		$this->assertTrue($this->html->find('html', 0)->hasChildNodes());
		$this->assertFalse($this->html->find('p', 0)->hasChildNodes());
	}

	public function test_hasAttribute_should_return_boolean()
	{
		$doc = '<html lang="en"></html>';

		$this->html->load($doc);

		$this->assertTrue($this->html->find('html', 0)->hasAttribute('lang'));
		$this->assertFalse($this->html->find('html', 0)->hasAttribute('encoding'));
	}

	public function test_hasClass_should_return_true()
	{
		$this->html->load('<p class="article">Simple HTML DOM Parser</p>');

		$this->assertTrue($this->html->find('p', 0)->hasClass('article'));
	}

	public function test_innertext_should_return_comment()
	{
		$expected = 'For your information';
		$doc = '<html><!--For your information--></html>';

		$this->html->load($doc);

		$this->assertEquals($expected, $this->html->find('comment', 0)->innertext());
	}

	public function test_hasClass_should_return_false()
	{
		$this->html->load('<p>Simple HTML DOM Parser</p>');

		$this->assertFalse($this->html->find('p', 0)->hasClass('article'));
	}

	public function test_addClass_should_add_class()
	{
		$expected = '<p class="article">Simple HTML DOM Parser</p>';

		$this->html->load('<p>Simple HTML DOM Parser</p>');
		$this->html->find('p', 0)->addClass('article');

		$this->assertEquals($expected, $this->html->save());
	}

	public function test_addClass_should_add_class_list()
	{
		$expected = '<p class="article new">Simple HTML DOM Parser</p>';

		$this->html->load('<p>Simple HTML DOM Parser</p>');
		$this->html->find('p', 0)->addClass('article new');

		$this->assertEquals($expected, $this->html->save());
	}

	public function test_addClass_should_add_class_array()
	{
		$expected = '<p class="article new">Simple HTML DOM Parser</p>';

		$this->html->load('<p>Simple HTML DOM Parser</p>');
		$this->html->find('p', 0)->addClass(array('article', 'new'));

		$this->assertEquals($expected, $this->html->save());
	}

	public function test_addClass_should_add_missing_classes()
	{
		$expected = '<p class="article new">Simple HTML DOM Parser</p>';

		$this->html->load('<p class="article">Simple HTML DOM Parser</p>');
		$this->html->find('p', 0)->addClass('article new');

		$this->assertEquals($expected, $this->html->save());
	}

	public function test_appendChild_should_add_node_as_child()
	{
		$expected = '<html><p>Simple HTML DOM Parser</p></html>';
		$doc = '<html></html>';

		$this->html->load($doc);
		$child = $this->html->createElement('p', 'Simple HTML DOM Parser');
		$this->html->find('html', 0)->appendChild($child);

		$this->assertEquals($expected, $this->html->save());
	}

	public function test_removeClass_should_do_nothing_without_class()
	{
		$expected = '<p>Simple HTML DOM Parser</p>';
		$doc = '<p>Simple HTML DOM Parser</p>';

		$this->html->load($doc);
		$this->html->find('p', 0)->removeClass('new');

		$this->assertEquals($expected, $this->html->save());
	}

	public function test_removeClass_should_remove_all_classes()
	{
		$expected = '<p>Simple HTML DOM Parser</p>';

		$this->html->load('<p class="article">Simple HTML DOM Parser</p>');
		$this->html->find('p', 0)->removeClass();

		$this->assertEquals($expected, $this->html->save());
	}

	public function test_removeClass_should_remove_class_attribute()
	{
		$expected = '<p>Simple HTML DOM Parser</p>';

		$this->html->load('<p class="article">Simple HTML DOM Parser</p>');
		$this->html->find('p', 0)->removeClass('article');

		$this->assertEquals($expected, $this->html->save());
	}

	public function test_removeClass_should_remove_class()
	{
		$expected = '<p class="article">Simple HTML DOM Parser</p>';

		$this->html->load('<p class="article new">Simple HTML DOM Parser</p>');
		$this->html->find('p', 0)->removeClass('new');

		$this->assertEquals($expected, $this->html->save());
	}

	public function test_removeClass_should_remove_class_list()
	{
		$expected = '<p>Simple HTML DOM Parser</p>';

		$this->html->load('<p class="article new">Simple HTML DOM Parser</p>');
		$this->html->find('p', 0)->removeClass('article new');

		$this->assertEquals($expected, $this->html->save());
	}

	public function test_removeClass_should_remove_class_array()
	{
		$expected = '<p>Simple HTML DOM Parser</p>';

		$this->html->load('<p class="article new">Simple HTML DOM Parser</p>');
		$this->html->find('p', 0)->removeClass(array('article', 'new'));

		$this->assertEquals($expected, $this->html->save());
	}

	public function test_save_should_return_outerhtml()
	{
		$expected = '<p>Simple HTML DOM Parser</p>';

		$this->html->load('<div><p>Simple HTML DOM Parser</p></div>');

		$this->assertEquals($expected, $this->html->find('p', 0)->save());
	}

	public function test_setAttribute_should_update_attribute()
	{
		$expected = 'de';
		$doc = '<html lang="en"></html>';

		$this->html->load($doc);
		$this->html->find('html', 0)->setAttribute('lang', 'de');

		$this->assertEquals($expected, $this->html->find('html', 0)->getAttribute('lang'));
	}

	public function test_text_after_clear_should_be_empty()
	{
		$doc = '<html></html>';

		$this->html->load($doc);
		$element = $this->html->find('html', 0);
		$element->clear();

		$this->assertEmpty($element->text());
	}

	public function textDataProvider()
	{
		return [
			'<script> should not be included' => [
				'<script>This should not be included</script>',
				''
			],
			'<style> should not be included' => [
				'<style>This should not be included</style>',
				''
			],
			'HTML comments should not be included' => [
				'<!--This should not be included-->',
				''
			],
			'CDATA should be included' => [
				'<![CDATA[This should be included]]>',
				'This should be included'
			],
			'<br> as first element should not add line break' => [
				'<br>',
				''
			],
			'<br> as first element inside <span> should not add line break as first element' => [
				'<span><br></span>',
				''
			],
			'<br> should add line break' => [
				'A<br>B',
				"A\r\nB"
			],
			'Text should not start with line break with <p> as first element' => [
				'<p></p>',
				''
			],
			'Empty <p> element should add line breaks' => [
				'A<p></p>B',
				"A\n\nB"
			],
			'<p> element should add line breaks before and after' => [
				'A<p>B</p>C',
				"A\n\nB\n\nC"
			],
			'Multiple empty <p> elements should collapse to a single set of line breaks' => [
				'A<p></p><p></p>B',
				"A\n\nB"
			],
			'Plain text nodes should be included' => [
				'A',
				'A'
			],
			'Whitespace at the beginning should be removed' => [
				" \t\n\r\0\x0B\xC2\xA0A",
				'A'
			],
			'Whitespace at the end should be removed' => [
				"A \t\n\r\0\x0B\xC2\xA0",
				'A'
			],
			'Spaces between elements should collapse to a single space' => [
				'A  <span>   B  </span>  C',
				'A B C'
			],
			'Carriage returns inside text should be returned as spaces' => [
				"<p>A\rB\rC</p>",
				'A B C'
			],
			'Newlines inside text should be returned as spaces' => [
				"<p>A\nB\nC</p>",
				'A B C'
			],
			'Tabs inside text should be returned as spaces' => [
				"<p>A\tB\tC</p>",
				'A B C'
			],
			'Vertical tabs inside text should be returned as spaces' => [
				"<p>A\vB\vC</p>",
				'A B C'
			],
			'Form feeds inside text should be returned as spaces' => [
				"<p>A\fB\fC</p>",
				'A B C'
			],
			'Multiple carriage returns inside text should collapse to a single whitespace' => [
				"<p>A\r\r\rB\r\r\rC</p>",
				'A B C'
			],
			'Multiple newlines inside text should collapse to a single whitespace' => [
				"<p>A\n\n\nB\n\n\nC</p>",
				'A B C'
			],
			'Multiple tabs inside text should collapse to a single whitespace' => [
				"<p>A\t\t\tB\t\t\tC</p>",
				'A B C'
			],
			'Multiple vertical tabs inside text should collapse to a single whitespace' => [
				"<p>A\v\v\vB\v\v\vC</p>",
				'A B C'
			],
			'Multiple form feeds inside text should collapse to a single whitespace' => [
				"<p>A\f\f\fB\f\f\fC</p>",
				'A B C'
			],
			'Whitespace inside and outside paragraph should collapse to single whitespace' => [
				"A\r\n\t\f\v<p>\r\n\f\vB</p>C",
				"A\n\nB\n\nC"
			],
			'MEDIUM MATHEMATICAL SPACE should be handled as whitespace' => [
				"A\u{205F}B\u{205F}C",
				"A B C"
			],
		];
	}

	/**
	 * @dataProvider textDataProvider
	 */
	public function test_text_after_load_should_return_expected_text($doc, $expected_text)
	{
		$this->html->load($doc);
		$this->assertEquals($expected_text, $this->html->root->text());
	}

	public function test_save_should_create_file()
	{
		$expected_file = __DIR__ . '/data/simple_html_dom_node/save_file_expected.html';
		$file = __DIR__ . '/data/simple_html_dom_node/save_file.html';

		$this->html->load('<div><p>Simple HTML DOM Parser</p></div>');
		$this->html->find('p', 0)->save($file);

		$this->assertFileExists($file);
		$this->assertFileEquals($expected_file, $file);
	}

	public function test_find_ancestor_tag_should_return_element()
	{
		$doc = '<html><p></p></html>';
		$this->html->load($doc);

		$this->assertEquals(
			$this->html->find('html', 0),
			$this->html->find('p', 0)->find_ancestor_tag('html')
		);
	}

	public function test_find_ancestor_tag_should_return_null_without_match()
	{
		$doc = '<html><p></p></html>';
		$this->html->load($doc);

		$this->assertNull($this->html->find('p', 0)->find_ancestor_tag('a'));
	}

	public function test_first_child_should_return_null_without_children()
	{
		$doc = '<html></html>';
		$this->html->load($doc);

		$this->assertNull($this->html->find('html', 0)->first_child());
		$this->assertNull($this->html->find('html', 0)->firstChild());
	}

	public function test_first_child_should_work_after_remove()
	{
		$doc = '<div><a href="#"></a><p></p></div>';

		$this->html->load($doc);
		$this->html->find('a', 0)->remove();

		$this->assertNotNull($this->html->find('div', 0)->first_child());
		$this->assertNotNull($this->html->find('div', 0)->firstChild());
	}

	public function test_getAttribute_should_return_attribute()
	{
		$expected = 'en';
		$doc = '<html lang="en"></html>';

		$this->html->load($doc);

		$this->assertEquals($expected, $this->html->find('html', 0)->getAttribute('lang'));
	}

	public function test_getElementById_should_return_matching_element()
	{
		$doc = '<html><p id="claim">PHP Simple HTML DOM Parser</p></html>';

		$this->html->load($doc);

		$this->assertNotNull($this->html->root->getElementById('claim'));
		$this->assertNull($this->html->root->getElementById('unknown'));
	}

	public function test_getElementsById_should_return_matching_element()
	{
		// Note, this technically doesn't make sense but it's supported
		$doc = '<html><p id="a"></p><p id="a"></p></html>';

		$this->html->load($doc);

		$this->assertCount(2, $this->html->root->getElementsById('a'));
	}

	public function test_getElementByTagName_should_return_matching_element()
	{
		$expected = 'Hello';
		$doc = '<html><p>Hello</p><p>World</p></html>';

		$this->html->load($doc);

		$this->assertEquals($expected, $this->html->root->getElementByTagName('p')->text());
	}

	public function test_getElementsByTagName_should_return_matching_element()
	{
		$doc = '<html><p>Hello</p><p>World</p></html>';

		$this->html->load($doc);

		$this->assertCount(2, $this->html->root->getElementsByTagName('p'));
	}

	public function test_last_child_should_return_null_without_children()
	{
		$doc = '<html></html>';
		$this->html->load($doc);

		$this->assertNull($this->html->find('html', 0)->last_child());
		$this->assertNull($this->html->find('html', 0)->lastChild());
	}

	public function test_last_child_should_work_after_remove()
	{
		$doc = '<div><a href="#"></a><p></p></div>';

		$this->html->load($doc);
		$this->html->find('p', 0)->remove();

		$this->assertNotNull($this->html->find('div', 0)->last_child());
		$this->assertNotNull($this->html->find('div', 0)->lastChild());
	}

	public function test_next_sibling_should_return_null_without_parent()
	{
		$doc = '<html></html>';
		$this->html->load($doc);

		$this->assertNull($this->html->root->next_sibling());
	}

	public function test_next_sibling_should_return_null_without_sibling()
	{
		$doc = '<html></html>';
		$this->html->load($doc);

		$this->assertNull($this->html->find('html', 0)->next_sibling());
	}

	public function test_outertext_should_return_innertext_for_root()
	{
		$doc = '<html><!--For your information--></html>';

		$this->html->load($doc);

		$this->assertEquals($doc, $this->html->root->outertext());
	}

	public function test_outertext_should_add_quotes_to_attribute_values_with_space()
	{
		$expected = '<e attribute="new' . ' ' . 'value">';

		$this->html->load('<e attribute=value>');
		$this->html->find('e', 0)->setAttribute('attribute', 'new value');

		$this->assertEquals($expected, $this->html->root->save());
	}

	public function test_outertext_should_add_quotes_to_attribute_values_with_tabulation()
	{
		$expected = '<e attribute="new' . "\t" . 'value">';

		$this->html->load('<e attribute=value>');
		$this->html->find('e', 0)->setAttribute('attribute', "new\tvalue");

		$this->assertEquals($expected, $this->html->root->save());
	}

	public function test_outertext_should_add_quotes_to_attribute_values_with_form_feed()
	{
		$expected = '<e attribute="new' . "\f" . 'value">';

		$this->html->load('<e attribute=value>');
		$this->html->find('e', 0)->setAttribute('attribute', "new\fvalue");

		$this->assertEquals($expected, $this->html->root->save());
	}

	public function test_outertext_should_add_quotes_to_attribute_values_with_carriage_return()
	{
		$expected = '<e attribute="new' . "\r" . 'value">';

		$this->html->load('<e attribute=value>');
		$this->html->find('e', 0)->setAttribute('attribute', "new\rvalue");

		$this->assertEquals($expected, $this->html->root->save());
	}

	public function test_outertext_should_add_quotes_to_attribute_values_with_line_feed()
	{
		$expected = '<e attribute="new' . "\n" . 'value">';

		$this->html->load('<e attribute=value>');
		$this->html->find('e', 0)->setAttribute('attribute', "new\nvalue");

		$this->assertEquals($expected, $this->html->root->save());
	}

	public function test_outertext_should_return_custom_outertext()
	{
		$expected = '<html>Hello, World!</html>';
		$doc = '<html><p>PHP Simple HTML DOM Parser</p></html>';

		$this->html->load($doc);
		$this->html->find('p', 0)->outertext = 'Hello, World!';

		$this->assertEquals($expected, $this->html->find('html', 0)->outertext());
	}

	/**
	 * @dataProvider rawTextElementProvider
	 */
	public function test_outertext_should_return_raw_text_for_raw_text_elements($element)
	{
		$doc = "<{$element}>var data = '<div>';</{$element}>";
		$this->html->load($doc);
		$this->assertEquals($doc, $this->html->root->outertext());
	}

	public function test_next_sibling_should_work_after_remove()
	{
		$doc = '<div><a href="#"></a><img><p></p></div>';

		$this->html->load($doc);
		$this->html->find('img', 0)->remove();

		$this->assertNotNull($this->html->find('a', 0)->next_sibling());
	}

	public function test_nodeName_should_return_tag_name()
	{
		$expected = 'div';
		$doc = '<div></div>';

		$this->html->load($doc);

		$this->assertEquals($expected, $this->html->find('div', 0)->nodeName());
	}

	public function test_parent_should_make_node_child_of_element()
	{
		$expected = '<html><p>PHP Simple HTML DOM Parser</p></html>';
		$doc = '<html></html>';

		$this->html->load($doc);
		$this->html->find('html', 0);

		$node = $this->html->createElement('p', 'PHP Simple HTML DOM Parser');
		$node->parent($this->html->find('html', 0));

		$this->assertEquals($expected, $this->html->save());
	}

	public function test_prev_sibling_should_return_null_without_parent()
	{
		$doc = '<html></html>';
		$this->html->load($doc);

		$this->assertNull($this->html->root->prev_sibling());
	}

	public function test_prev_sibling_should_return_null_without_sibling()
	{
		$doc = '<html></html>';
		$this->html->load($doc);

		$this->assertNull($this->html->find('html', 0)->prev_sibling());
	}

	public function test_prev_sibling_should_work_after_remove()
	{
		$doc = '<div><a href="#"></a><img><p></p></div>';

		$this->html->load($doc);
		$this->html->find('img', 0)->remove();

		$this->assertNotNull($this->html->find('p', 0)->prev_sibling());
	}

	public function test_children_should_return_element_by_index()
	{
		$expected = 'PHP Simple HTML DOM Parser';
		$doc = '<html><p>PHP Simple HTML DOM Parser</p></html>';
		$this->html->load($doc);

		$this->assertEquals($expected, $this->html->find('html', 0)->children(0)->text());
		$this->assertEquals($expected, $this->html->find('html', 0)->childNodes(0)->text());
	}

	public function test_children_should_return_null_if_index_out_of_range()
	{
		$doc = '<html><p>PHP Simple HTML DOM Parser</p></html>';
		$this->html->load($doc);

		$this->assertNull($this->html->find('html', 0)->children(1));
		$this->assertNull($this->html->find('html', 0)->childNodes(1));
	}

	public function test_children_should_work_after_remove()
	{
		$doc = '<div><a href="#"></a><img><p></p></div>';

		$this->html->load($doc);
		$this->html->find('img', 0)->remove();

		$this->assertCount(2, $this->html->find('div', 0)->children());
		$this->assertArrayHasKey(0, $this->html->find('div', 0)->children());
		$this->assertArrayHasKey(1, $this->html->find('div', 0)->children());

		$this->assertCount(2, $this->html->find('div', 0)->childNodes());
		$this->assertArrayHasKey(0, $this->html->find('div', 0)->childNodes());
		$this->assertArrayHasKey(1, $this->html->find('div', 0)->childNodes());
	}

	public function test_expect_should_return_null_for_no_match()
	{
		$doc = '<div><a href="#"></a><img><p></p></div>';

		$this->html->load($doc);
		$this->html->find('img', 0)->remove();

		$this->assertNull($this->html->expect('p.class'));
		$this->assertNull($this->html->expect('p.class', 0));
	}

	public function test_toString_should_return_html_document()
	{
		$doc = '<p>PHP Simple HTML DOM Parser</p>';

		$this->html->load($doc);

		$this->assertEquals($doc, sprintf('%s', $this->html->find('p', 0)));
	}

	public function test_xmltext_should_strip_cdata_tags()
	{
		$expected = '<p><em>PHP Simple HTML DOM Parser</em></p>';
		$doc = '<p><![CDATA[<em>]]>PHP Simple HTML DOM Parser<![CDATA[</em>]]></p>';

		$this->html->load($doc);

		$this->assertEquals($expected, $this->html->root->xmltext);
		$this->assertEquals($expected, $this->html->root->xmltext());
	}

}

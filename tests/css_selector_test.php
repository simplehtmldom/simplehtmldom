<?php
require_once __DIR__ . '/../simple_html_dom.php';
use PHPUnit\Framework\TestCase;

/**
 * Tests for CSS selectors based on the current CSS Specification
 *
 * @link https://www.w3.org/TR/selectors/ CSS Selectors Specification
 */
class css_selector_test extends TestCase {

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
	 * pattern: "*"
	 * Represents any element
	 *
	 * @link https://www.w3.org/TR/selectors/#the-universal-selector
	 * Universal Selector
	 */
	public function test_universal()
	{
		$doc = <<<HTML
<html>
<head><title>PHP Simple HTML DOM Parser</title></head>
<body><h1>A PHP based DOM parser</h1></body>
</html>
HTML;

		$this->html->load($doc);

		$this->assertCount(5, $this->html->find('*'));
	}

	/**
	 * pattern: "E"
	 * Represents an element of type E
	 *
	 * @link https://www.w3.org/TR/selectors/#type-selectors
	 * Type Selector
	 */
	public function test_type()
	{
		$doc = <<<HTML
<html>
<head><title>PHP Simple HTML DOM Parser</title></head>
<body><h1>A PHP based DOM parser</h1></body>
</html>
HTML;

		$this->html->load($doc);

		$this->assertCount(1, $this->html->find('html'));
		$this->assertCount(1, $this->html->find('head'));
		$this->assertCount(1, $this->html->find('title'));
		$this->assertCount(1, $this->html->find('body'));
		$this->assertCount(1, $this->html->find('h1'));

		// This should not exist
		$this->assertCount(0, $this->html->find('div'));
	}

	/**
	 * pattern: "E.warning"
	 * Represents an E element belonging to the class warning (the document
	 * language specifies how class is determined).
	 *
	 * @link https://www.w3.org/TR/selectors/#class-html
	 * Class selectors
	 */
	public function test_class()
	{
		$doc = <<<HTML
<html>
<body>
	<p class="title header">PHP Simple HTML DOM Parser</p>
	<p class="subtitle">A PHP based DOM parser</p>
</body>
</html>
HTML;

		$this->html->load($doc);

		$this->assertCount(1, $this->html->find('p.title'));
		$this->assertCount(1, $this->html->find('p.subtitle'));
		$this->assertCount(1, $this->html->find('p.title.header'));
	}

	public function test_class_should_skip_tags_without_classes()
	{
		$doc = <<<HTML
<html>
<body>
	<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
</body>
</html>
HTML;

		$this->html->load($doc);

		$this->assertCount(0, $this->html->find('p.title'));
	}

	public function test_class_should_find_camel_case()
	{
		$doc = <<<HTML
<html>
<body>
	<p class="myClass">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
</body>
</html>
HTML;

		$this->html->load($doc);

		$this->assertCount(1, $this->html->find('p.myClass'));
	}

	/**
	 * pattern: "E#myid"
	 * Represents an E element with ID equal to myid.
	 *
	 * @link https://www.w3.org/TR/selectors/#id-selectors
	 * ID selectors
	 */
	public function test_id()
	{
		$doc = <<<HTML
<html>
<body>
	<p id="title">PHP Simple HTML DOM Parser</p>
	<p id="subtitle">A PHP based DOM parser</p>
</body>
</html>
HTML;

		$this->html->load($doc);

		$this->assertCount(1, $this->html->find('p#title'));
		$this->assertCount(1, $this->html->find('p#subtitle'));
	}

	public function test_id_selector_should_find_camel_case()
	{
		$doc = <<<HTML
<html>
<body>
	<p id="myMessage">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
</body>
</html>
HTML;

		$this->html->load($doc);

		$this->assertCount(1, $this->html->find('p#myMessage'));
	}

	#region Attribute

	/**
	 * pattern: "E[foo]"
	 * Represents an E element with a foo attribute
	 *
	 * @link https://www.w3.org/TR/selectors/#attribute-selectors
	 * Attribute selectors
	 */
	public function test_attribute_exists()
	{
		$doc = <<<HTML
<html>
<body>
	<p id="title" class="h1">PHP Simple HTML DOM Parser</p>
	<p id="subtitle" class="h2" style="color:blue;">A PHP based DOM parser</p>
</body>
</html>
HTML;

		$this->html->load($doc);

		$this->assertCount(2, $this->html->find('p[id]'));
		$this->assertCount(2, $this->html->find('p[id][class]'));
		$this->assertCount(1, $this->html->find('p[id][class][style]'));
	}

	/**
	 * pattern: "E[foo="bar"]"
	 * Represents an E element whose foo attribute value is exactly equal to bar
	 *
	 * @link https://www.w3.org/TR/selectors/#attribute-selectors
	 * Attribute selectors
	 */
	public function test_attribute_value_equals()
	{
		$doc = <<<HTML
<html>
<body>
	<p id="title" class="h1">PHP Simple HTML DOM Parser</p>
	<p id="subtitle" class="h2" style="color:blue;">A PHP based DOM parser</p>
</body>
</html>
HTML;

		$this->html->load($doc);

		$this->assertCount(1, $this->html->find('p[id="title"]'));
		$this->assertCount(1, $this->html->find('p[id="subtitle"]'));
		$this->assertCount(1, $this->html->find('p[id="title"][class="h1"]'));
		$this->assertCount(1, $this->html->find('p[id="subtitle"][class="h2"][style="color:blue;"]'));
	}

	/**
	 * pattern: "E[foo="bar" i]"
	 * Represents an E element whose foo attribute value is exactly equal to any
	 * (ASCII-range) case-permutation of bar
	 *
	 * @link https://www.w3.org/TR/selectors/#attribute-case
	 * Attribute case
	 */
	public function test_attribute_value_equals_case_insensitive()
	{
		$doc = <<<HTML
<html lang="en-US">
<body>
	<p att="title">PHP Simple HTML DOM Parser</p>
	<p att="tItle">A PHP based DOM parser</p>
</body>
</html>
HTML;

		$this->html->load($doc);

		$this->assertCount(1, $this->html->find('p[att="title"]'));
		$this->assertCount(2, $this->html->find('p[att="title" i]'));

		$this->assertCount(0, $this->html->find('p[att^="TITLE"]'));
		$this->assertCount(2, $this->html->find('p[att^="TITLE" i]'));

		$this->assertCount(0, $this->html->find('p[att$="LE"]'));
		$this->assertCount(2, $this->html->find('p[att$="LE" i]'));

		$this->assertCount(0, $this->html->find('p[att*="ITL"]'));
		$this->assertCount(2, $this->html->find('p[att*="ITL" i]'));

		$this->assertCount(0, $this->html->find('html[lang|="EN"]'));
		$this->assertCount(1, $this->html->find('html[lang|="EN" i]'));
	}

	/**
	 * pattern: "E[foo="bar" s]"
	 * Represents an E element whose foo attribute value is exactly and
	 * case-sensitively equal to bar
	 *
	 * @link https://www.w3.org/TR/selectors/#attribute-case
	 * Attribute case
	 */
	public function test_attribute_value_equals_case_sensitive()
	{
		$doc = <<<HTML
<html lang="en-US">
<body>
	<p att="title header">PHP Simple HTML DOM Parser</p>
	<p att="tItle">A PHP based DOM parser</p>
</body>
</html>
HTML;

		$this->html->load($doc);

		$this->assertCount(0, $this->html->find('p[att="title" s]'));
		$this->assertCount(1, $this->html->find('p[att="tItle" s]'));
	}

	/**
	 * pattern: "E[foo~="bar"]"
	 * Represents an E element whose foo attribute value is a list of
	 * whitespace-separated values, one of which is exactly equal to bar
	 *
	 * @link https://www.w3.org/TR/selectors/#attribute-selectors
	 * Attribute selectors
	 */
	public function test_attribute_value_list_contains()
	{
		$doc = <<<HTML
<html>
<body>
	<p att="title header">PHP Simple HTML DOM Parser</p>
	<p att="title subtitle">A PHP based DOM parser</p>
</body>
</html>
HTML;

		$this->html->load($doc);

		$this->assertCount(2, $this->html->find('p[att~="title"]'));
		$this->assertCount(1, $this->html->find('p[att~="header"]'));
		$this->assertCount(1, $this->html->find('p[att~="subtitle"]'));
		$this->assertCount(0, $this->html->find('p[att~=" title"'));
		$this->assertCount(0, $this->html->find('p[att~=" "'));
	}

	/**
	 * pattern: "E[foo^="bar"]"
	 * Represents an E element whose foo attribute value begins exactly with the
	 * string bar
	 *
	 * @link https://www.w3.org/TR/selectors/#attribute-substrings
	 * Attribute selectors
	 */
	public function test_attribute_value_begins()
	{
		$doc = <<<HTML
<html>
<body>
	<p id="title">PHP Simple HTML DOM Parser</p>
	<p id="subtitle">A PHP based DOM parser</p>
</body>
</html>
HTML;

		$this->html->load($doc);

		$this->assertCount(1, $this->html->find('p[id^="sub"]'));
	}

	/**
	 * pattern: "E[foo$="bar"]"
	 * Represents an E element whose foo attribute value ends exactly with the
	 * string bar
	 *
	 * @link https://www.w3.org/TR/selectors/#attribute-substrings
	 * Attribute substrings
	 */
	public function test_attribute_value_ends()
	{
		$doc = <<<HTML
<html>
<body>
	<p id="title">PHP Simple HTML DOM Parser</p>
	<p id="subtitle">A PHP based DOM parser</p>
</body>
</html>
HTML;

		$this->html->load($doc);

		$this->assertCount(2, $this->html->find('p[id$="title"]'));
	}

	/**
	 * pattern: "E[foo*="bar"]"
	 * Represents an E element whose foo attribute value contains the substring
	 * bar
	 *
	 * @link https://www.w3.org/TR/selectors/#attribute-substrings
	 * Attribute substrings
	 */
	public function test_attribute_value_contains()
	{
		$doc = <<<HTML
<html>
<body>
	<p id="title">PHP Simple HTML DOM Parser</p>
	<p id="subtitle">A PHP based DOM parser</p>
	<p class="title header">PHP Simple HTML DOM Parser</p>
	<p class="title subtitle">A PHP based DOM parser</p>
</body>
</html>
HTML;

		$this->html->load($doc);

		$this->assertCount(2, $this->html->find('p[id*="itl"]'));
		$this->assertCount(2, $this->html->find('p[class*="title"]'));
		$this->assertCount(1, $this->html->find('p[class*="title header"]'));
		$this->assertCount(1, $this->html->find('p[class*="subtitle"]'));
	}

	/**
	 * pattern: "E[foo|="en"]"
	 * Represents an E element whose foo attribute value is a hyphen-separated
	 * list of values beginning with en
	 *
	 * @link https://www.w3.org/TR/selectors/#attribute-selectors
	 * Attribute selectors
	 */
	public function test_attribute_value_list_begins()
	{
		$doc = <<<HTML
<a href="#" hreflang="en-US">en-US</a>
<a href="#" hreflang="en-UK">en-UK</a>
<a href="#" hreflang="en">en</a>
<a href="#" hreflang="fr">fr</a>
HTML;

		$this->html->load($doc);

		$anchors = $this->html->find('a[hreflang|="en"]');

		$this->assertCount(3, $anchors);
		$this->assertEquals('en-US', $anchors[0]->innertext);
		$this->assertEquals('en-UK', $anchors[1]->innertext);
		$this->assertEquals('en', $anchors[2]->innertext);
	}

	#endregion Attribute

	#region Combinator

	/**
	 * pattern: "E F"
	 * Represents an F element descendant of an E element
	 *
	 * @link https://www.w3.org/TR/selectors/#descendant-combinators
	 * Descendant combinators
	 */
	public function test_descendant_combinators()
	{
		$doc = <<<HTML
<html>
<body>
	<p id="title">PHP Simple HTML DOM Parser</p>
	<p id="subtitle">A PHP based DOM parser</p>
</body>
</html>
HTML;

		$this->html->load($doc);

		$this->assertCount(2, $this->html->find('html body p'));
	}

	/**
	 * pattern: "E > F"
	 * Represents an F element child of an E element
	 *
	 * @link https://www.w3.org/TR/selectors/#child-combinators
	 * Child combinators
	 */
	public function test_child_combinators()
	{
		$doc = <<<HTML
<html>
<body>
	<p id="title">PHP Simple HTML DOM Parser</p>
	<p id="subtitle">A PHP based DOM parser</p>
	<div>
		<p>Lorem ipsum dolor sit amet.</p>
	</div>
</body>
</html>
HTML;

		$this->html->load($doc);

		$this->assertCount(2, $this->html->find('html > body > p'));
	}

	/**
	 * pattern: "E + F"
	 * Represents an F element immediately preceded by an E element
	 *
	 * @link https://www.w3.org/TR/selectors/#adjacent-sibling-combinators
	 * Next sibling combinators
	 */
	public function test_next_sibling_combinators()
	{
		$doc = <<<HTML
<html>
<body>
	<h1>PHP Simple HTML DOM Parser</h1>
	<p id="title">PHP Simple HTML DOM Parser</p>
	<h2>A PHP based DOM parser</h2>
	<p id="subtitle">A PHP based DOM parser</p>
</body>
</html>
HTML;

		$this->html->load($doc);

		$this->assertCount(1, $this->html->find('h1 + p'));
		$this->assertCount(1, $this->html->find('h2 + p'));
	}

	/**
	 * pattern: "E ~ F"
	 * Represents an F element preceded by an E element
	 *
	 * @link https://www.w3.org/TR/selectors/#general-sibling-combinators
	 * General sibling combinators
	 */
	public function test_general_sibling_combinators()
	{
		$doc = <<<HTML
<html>
<body>
	<h1>PHP Simple HTML DOM Parser</h1>
	<p id="title">PHP Simple HTML DOM Parser</p>
	<h2>A PHP based DOM parser</h2>
	<p id="subtitle">A PHP based DOM parser</p>
</body>
</html>
HTML;

		$this->html->load($doc);

		$this->assertCount(2, $this->html->find('h1 ~ p'));
		$this->assertCount(1, $this->html->find('h2 ~ p'));
	}

	#endregion Combinator

	#region Pseudo Classes

	/**
	 * pattern: "E:not(s)"
	 * Represents an E element that does not match simple selector s
	 *
	 * @link https://www.w3.org/TR/selectors-3/#negation
	 * Negation pseudo class
	 */
	public function test_negation_pseudo_class()
	{
		$doc = <<<HTML
<html>
<body>
	<h1>PHP Simple HTML DOM Parser</h1>
	<p id="title">PHP Simple HTML DOM Parser</p>
	<h2>A PHP based DOM parser</h2>
	<p id="subtitle">A PHP based DOM parser</p>
</body>
</html>
HTML;

		$this->html->load($doc);

		$this->assertCount(1, $this->html->find('p:not([id="title"])'));
		$this->assertCount(5, $this->html->find(':not(p[id="subtitle"])'));
		$this->assertCount(3, $this->html->find('body :not(p[id="title"])'));
	}

	#endregion Pseudo Classes

	/**
	 * "comment", "cdata" and "text" selectors are specific to this parser. They
	 * allow users to directly address these nodes and extract useful information.
	 *
	 * @dataProvider dataProvider_for_find_should_work_with_special_selector
	 */
	public function test_find_should_work_with_special_selector($selector, $expected, $doc, $message)
	{
		$this->html->load($doc);
		$this->assertEquals($expected, $this->html->find($selector, 0)->innertext, $message);
		$this->assertEquals($doc, $this->html->save());
	}

	public function dataProvider_for_find_should_work_with_special_selector()
	{
		$data = array(
			'text without elements' => array(
				'text',
				'Hello, World!',
				'Hello, World!',
				'find should return text without elements'
			),
			'text outside html' => array(
				'text',
				'Hello, World!',
				'Hello, World!<html></html>',
				'find should return text outside html'
			),
			'text inside element' => array(
				'text',
				'Hello, World!',
				'<html>Hello, World!</html>',
				'find should return text inside element'
			),
			'text between elements' => array(
				'text',
				'Hello, World!',
				'<html><head></head>Hello, World!<body></body></html>',
				'find should return text between elements'
			),
			'cdata without elements' => array(
				'cdata',
				'Hello, World!',
				'<![CDATA[Hello, World!]]>',
				'find should return cdata elements'
			),
			'cdata outside html' => array(
				'cdata',
				'Hello, World!',
				'<![CDATA[Hello, World!]]><html></html>',
				'find should return cdata elements'
			),
			'cdata inside element' => array(
				'cdata',
				'Hello, World!',
				'<html><![CDATA[Hello, World!]]></html>',
				'find should return cdata elements'
			),
			'cdata between elements' => array(
				'cdata',
				'Hello, World!',
				'<html><head></head><![CDATA[Hello, World!]]><body></body></html>',
				'find should return cdata elements'
			),
			'comment without elements' => array(
				'comment',
				'Hello, World!',
				'<!--Hello, World!-->',
				'find should return comments'
			),
			'comment outside html' => array(
				'comment',
				'Hello, World!',
				'<!--Hello, World!--><html></html>',
				'find should return comments'
			),
			'comment inside element' => array(
				'comment',
				'Hello, World!',
				'<html><!--Hello, World!--></html>',
				'find should return comments'
			),
			'comment between elements' => array(
				'comment',
				'Hello, World!',
				'<html><head></head><!--Hello, World!--><body></body></html>',
				'find should return comments'
			)
		);

		return $data;
	}
}

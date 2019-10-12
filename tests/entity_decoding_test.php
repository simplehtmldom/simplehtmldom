<?php
require_once __DIR__ . '/../simple_html_dom.php';
use PHPUnit\Framework\TestCase;

/**
 * Tests for simple_html_dom entity decoding
 */
class entity_decoding_test extends TestCase {
	/**
	 * @dataProvider load_should_decode_entity_dataProvider
	 */
	public function test_load_should_decode_entity($name, $char, $expected)
	{
		$this->assertEquals($expected, $char, 'Character: ' . $name);
	}

	public function load_should_decode_entity_dataProvider()
	{
		$file = __DIR__ . '/data/entity_decoding/Character Entity Reference Chart.html';

		// This operation is very slow due to missing closing tags
		$html = new simple_html_dom();
		$html->loadFile($file);

		$table = get_html_translation_table(HTML_ENTITIES, ENT_QUOTES | ENT_HTML5, $html->target_charset);

		$vector = array();

		foreach($html->find('table tr') as $tr) {
			$char = $tr->find('td.character', 0)->innertext;
			$char = substr($char, 1); /* first character is always space */

			$name = $tr->find('td.named > code', 0)->plaintext;
			$name = explode(' ', $name)[0]; /* may contain multiple representations */

			$expected = array_search($name, $table, true);

			if ($expected === false) continue; /* Unknown entity */

			$vector[] = array(
				$name,
				$char,
				$expected
			);
		}

		return $vector;
	}

	public function test_decode_should_decode_attributes()
	{
		$expected = 'Häagen-Dazs';

		$html = new simple_html_dom();
		$html->load('<meta name="description" content="H&auml;agen-Dazs">');

		$description = $html->find('meta[name="description"]', 0);

		$this->assertEquals($expected, $description->content);
	}

}

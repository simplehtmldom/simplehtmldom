<?php
/**
 * This example loads a page from Slashdot and displays articles in a custom
 * format.
 */
include_once '../../simple_html_dom.php';

// (optional) Use a custom user agent for your application
ini_set('user_agent', 'simplehtmldom_examples/1.0');

// Load the page into memory
$html = file_get_html('https://slashdot.org/');

// Find and extract all articles
foreach($html->find('#firehoselist > [id^="firehose-"]') as $article) {
	$item['title'] = trim($article->find('[id^="title-"]', 0)->plaintext);
	$item['body'] = trim($article->find('[id^="text-"]', 0)->plaintext);

	$data[] = $item;
}

// clean up memory
$html->clear();
unset($html);

// Return custom page
foreach($data as $item) {
	echo <<<EOD

<h2>{$item['title']}</h2>
<p>{$item['body']}</p>

EOD;
}

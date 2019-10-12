<?php
/**
 * This example loads a page from Slashdot and displays articles in a custom
 * format.
 */
include_once '../../HtmlWeb.php';
use simplehtmldom\HtmlWeb;

// Load the page into memory
$doc = new HtmlWeb();
$html = $doc->load('https://slashdot.org/');

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

<?php
/**
 * This example loads the main page from https://digg.com/, extracts news items
 * and returns the details in a custom format.
 */
include_once '../../HtmlWeb.php';
use simplehtmldom\HtmlWeb;

// Download a page
$doc = new HtmlWeb();
$html = $doc->load('https://digg.com/');

// Loop through all articles in the page
foreach($html->find('article') as $article) {

	// Find the title of the current article
	if($title = $article->find('h2', 0)) {
		$item['title'] = trim($title->plaintext);
	} else {
		$item['title'] = 'Unknown title';
	}

	// Find the description of the current article
	if($details = $article->find('div.description', 0)) {
		$item['details'] = trim($details->plaintext);
	} else {
		$item['details'] = '...';
	}

	// Find the tags for the current article
	if($diggs = $article->find('a[rel="tag"]', 0)) {
		$item['diggs'] = trim($diggs->plaintext);
	} else {
		$item['diggs'] = '';
	}

	$data[] = $item;
}

// (optional) Release memory
$html->clear();
unset($html);

// Display your own page to the user
foreach($data as $item) {
	echo <<<EOD

<h2>{$item['title']}</h2>
<ul>
<li>{$item['details']}</li>
<li>{$item['diggs']}</li>
</ul>

EOD;
}

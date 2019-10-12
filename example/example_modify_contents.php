<?php
// This example illustrates how to modify HTML contents
include_once '../HtmlWeb.php';
use simplehtmldom\HtmlWeb;

// Load the document
$doc = new HtmlWeb();
$html = $doc->load('https://www.google.com/');

// Remove all images and inputs from the DOM
foreach($html->find('img, input') as $element) {
	$element->remove();
}

echo $html;

<?php
// This example illustrates how to modify HTML contents
include_once '../simple_html_dom.php';

// Load the document
$html = file_get_html('https://www.google.com/');

// Remove all images and inputs from the DOM
foreach($html->find('img, input') as $element) {
	$element->remove();
}

echo $html;

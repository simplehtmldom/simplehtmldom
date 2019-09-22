<?php
// This file illustrates how to utilize the callback feature to manipulate the DOM
include_once '../simple_html_dom.php';

// Write a callback function with one parameter for the element
function my_callback($element) {
	if ($element->tag === 'a') { // Invalidate all anchors
		$element->href = '#';
	}
}

// Load the document
$html = file_get_html('https://www.google.com/');

// Register the callback function
$html->set_callback('my_callback');

// The callback function is invoked automatically when accessing the contents.
echo $html;

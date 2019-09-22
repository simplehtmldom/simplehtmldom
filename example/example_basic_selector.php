<?php
// This file illustrates how to use basic selectors to retrieve HTML contents
include_once '../simple_html_dom.php';

// get DOM from URL or file
$html = file_get_html('http://www.google.com/');

// find all links
foreach($html->find('a') as $e)
	echo $e->href . '<br>' . PHP_EOL;

// find all images
foreach($html->find('img') as $e)
	echo $e->src . '<br>' . PHP_EOL;

// find all images with full tag
foreach($html->find('img') as $e)
	echo $e->outertext . '<br>' . PHP_EOL;

// find all div tags with id="gbar"
foreach($html->find('div#gbar') as $e)
	echo $e->innertext . '<br>' . PHP_EOL;

// find all span tags with class="gb1"
foreach($html->find('span.gb1') as $e)
	echo $e->outertext . '<br>' . PHP_EOL;

// find all td tags with attribute align="center"
foreach($html->find('td[align=center]') as $e)
	echo $e->innertext . '<br>' . PHP_EOL;

// extract text from table
echo $html->find('td[align="center"]', 1)->plaintext . '<br><hr>' . PHP_EOL;

// extract text from HTML
echo $html->plaintext . PHP_EOL;

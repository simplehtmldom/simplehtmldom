<?php
// This example illustrates how to use basic selectors to retrieve HTML contents
include_once '../HtmlWeb.php';
use simplehtmldom\HtmlWeb;

// get DOM from URL or file
$doc = new HtmlWeb();
$html = $doc->load('http://www.google.com/');

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

// extract text from HTML
echo $html->plaintext . PHP_EOL;

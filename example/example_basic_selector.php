<?php
// example of how to use basic selector to retrieve HTML contents
include('../html_dom_parser.php');
 
// get DOM from URL or file
$dom = file_get_dom('http://www.google.com/');

// find all link
foreach($dom->find('a') as $node) 
    echo $node->href . '<br>';

// find all image
foreach($dom->find('img') as $node)
    echo $node->src . '<br>';

// find all div tags with id=gbar
foreach($dom->find('div#gbar') as $node)
    echo $node->innertext . '<br>';

// find all span tags with class=gb1
foreach($dom->find('span.gb1') as $node)
    echo $node->outertext . '<br>';

// find all td tags with attribite align=center
foreach($dom->find('td[align=center]') as $node)
    echo $node->innertext . '<br>';
?>
<?
// example of how to modify HTML contents 
include('html_dom_parser.php');

// get DOM from URL or file
$dom = file_get_dom('http://www.google.com/');

// remove all image
foreach($dom->find('img') as $node)
    $node->outertext = '';

// replace all input
foreach($dom->find('input') as $node)
    $node->outertext = '[INPUT]';

// dump contents
echo $dom->save();
?>
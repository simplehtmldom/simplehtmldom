<?
// example of how to modify HTML contents 
include('html_dom_parser.php');
 
// get DOM from URL or file
$dom = file_get_dom('http://www.google.com/');

// remove all image
$ret = $dom->find('img');
foreach($ret as $v) {$v->outertext = '';}

// replace all input
$ret = $dom->find('input');
foreach($ret as $v) {$v->outertext = '[INPUT]';}
 
// dump contents
echo $dom->save();
?>
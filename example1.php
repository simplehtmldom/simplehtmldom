<?
// example of how to retrieve HTML contents
include('html_dom_parser.php');
 
// get DOM from URL or file
$dom = file_get_dom('http://www.google.com/');
 
// find all link
$ret = $dom->find('a');
foreach($ret as $v) {echo $v->href . '<br>';}
 
// find all image
$ret = $dom->find('img');
foreach($ret as $v) {echo $v->src . '<br>';}
 
// find all div tags with id=gbar
$ret = $dom->find('div#gbar');
foreach($ret as $v) {echo $v->innertext . '<br>';}

// find all span tags with class=gb1
$ret = $dom->find('span.gb1');
foreach($ret as $v) {echo $v->outertext . '<br>';}
 
// find all td tags with attribite align=center
$ret = $dom->find('td[align=center]');
foreach($ret as $v) {echo $v->outertext . '<br>';}

// traverse all DOM nodes
$count = 0;
foreach($dom->nodes as $v) {++$count;}
echo 'Total DOM nodes: ' . $count;
?>
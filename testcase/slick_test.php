<?php
error_reporting(E_ALL);
include_once('../html_dom_parser.php');
$dom = file_get_dom('slickspeed.htm');

assert(count($dom->find('#title'))==1);
assert(count($dom->find('div'))==51);
assert(count($dom->find('div[class]'))==51);
assert(count($dom->find('div.example'))==43);
assert(count($dom->find('div[class=example]'))==43);
assert(count($dom->find('.note'))==14);

assert(count($dom->find('div[class^=exa]'))==43);
assert(count($dom->find('div[class$=mple]'))==43);
assert(count($dom->find('div[class*=e]'))==50);
assert(count($dom->find('div[class!=made_up]'))==51);

assert(count($dom->find('p'))==324);

/*
echo count($dom->find('a'));
foreach($dom->find('a') as $e) {
    echo $e->href.'<br>';
}
*/
echo 'All pass!<br>';
?>
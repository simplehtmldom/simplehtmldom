<?php
error_reporting(E_ALL);
include_once('../html_dom_parser.php');


echo '[multi objects clear memory]<br>init memory: '.memory_get_usage().'<br>';
echo '------------------------------------------<br>';
for($i=0; $i<3; ++$i) {
    $dom = file_get_dom('http://www.google.com.tw/');
    $dom->clear();
    unset($dom);
    echo 'memory: '.memory_get_usage().'<br>';
}
echo 'final memory: '.memory_get_usage().'<br>';
flush();



echo '<br><br>[one object]<br>init memory: '.memory_get_usage().'<br>';
echo '------------------------------------------<br>';
$dom = new html_dom_parser;
for($i=0; $i<3; ++$i) {
    $dom->load_file('http://www.google.com.tw/');
    echo 'memory: '.memory_get_usage().'<br>';
    $dom->clear();
}
unset($dom);
echo 'final memory: '.memory_get_usage().'<br>';
flush();



echo '<br><br>[multi objects no clear]<br>init memory: '.memory_get_usage().'<br>';
echo '------------------------------------------<br>';
for($i=0; $i<3; ++$i) {
    $dom = file_get_dom('http://www.google.com.tw/');
    echo 'memory: '.memory_get_usage().'<br>';
}
echo 'final memory: '.memory_get_usage().'<br>';
flush();
?>
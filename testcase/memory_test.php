<?php
error_reporting(E_ALL);
include_once('../simple_html_dom.php');

$mem_init = memory_get_usage();
echo '[multi objects clear memory]<br>init memory: '.$mem_init.'<br>';
echo '------------------------------------------<br>';
flush();
for($i=0; $i<10; ++$i) {
    $filename = './html/google.htm';
    $dom = file_get_dom($filename);
    /*
    $mem_curr = memory_get_usage();
    $mem_file = filesize($filename);
    echo 'memory: '.$mem_curr.'('.($mem_curr-$mem_init).') ---- ';
    echo 'file: '.$mem_file .'('.(($mem_curr-$mem_init)/$mem_file).')<br>';
    */
    $dom->clear();
    unset($dom);
    echo 'memory: '.memory_get_usage().'<br>';
}
echo 'final memory: '.memory_get_usage().'<br>';
flush();

echo '<br><br>[one object]<br>init memory: '.memory_get_usage().'<br>';
echo '------------------------------------------<br>';
flush();
$dom = new simple_html_dom;
for($i=0; $i<3; ++$i) {
    $dom->load_file('http://www.google.com.tw/');
    echo 'memory: '.memory_get_usage().'<br>';
}
$dom->clear();
unset($dom);
echo 'final memory: '.memory_get_usage().'<br>';
flush();



echo '<br><br>[multi objects without clear memory]<br>init memory: '.memory_get_usage().'<br>';
echo '------------------------------------------<br>';
flush();
for($i=0; $i<3; ++$i) {
    $dom = file_get_dom('http://www.google.com.tw/');
    echo 'memory: '.memory_get_usage().'<br>';
}
echo 'final memory: '.memory_get_usage().'<br>';
flush();
?>
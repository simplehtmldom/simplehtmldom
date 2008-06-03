<?php
error_reporting(E_ALL);
include_once('../simple_html_dom.php');

$filename = './html/google.htm';

function dump_memory() {
    echo 'peak: ' . number_format(memory_get_peak_usage(), 0, '.', ',') . ' bytes, end: ' . number_format(memory_get_usage(), 0, '.', ',') . " bytes<br>";
}

echo 'init ' . number_format(memory_get_usage(), 0, '.', ',') . " bytes";

echo '<br><br>[load file]<br>init memory: '.number_format(memory_get_usage(), 0, '.', ',').'<br>';
echo '------------------------------------------<br>';
flush();
for($i=0; $i<3; ++$i) {
    $str = file_get_contents($filename);
    unset($str);
    dump_memory();
}
echo 'final: '.number_format(memory_get_usage(), 0, '.', ',').'<br>';
flush();

echo '<br><br>[multi objects clear memory]<br>init memory: '.number_format(memory_get_usage(), 0, '.', ',').'<br>';
echo '------------------------------------------<br>';
flush();
for($i=0; $i<3; ++$i) {
    $dom = file_get_dom($filename);
    $dom->clear();
    unset($dom);
    dump_memory();
}
echo 'final: '.number_format(memory_get_usage(), 0, '.', ',').'<br>';
flush();


echo '<br><br>[one object]<br>init memory: '.number_format(memory_get_usage(), 0, '.', ',').'<br>';
echo '------------------------------------------<br>';
flush();
$dom = new simple_html_dom;
for($i=0; $i<3; ++$i) {
    $dom->load_file($filename);
    dump_memory();
}
$dom->clear();
unset($dom);
echo 'final: '.number_format(memory_get_usage(), 0, '.', ',').'<br>';
flush();



echo '<br><br>[multi objects without clear memory]<br>init memory: '.number_format(memory_get_usage(), 0, '.', ',').'<br>';
echo '------------------------------------------<br>';
flush();
for($i=0; $i<3; ++$i) {
    $dom = file_get_dom($filename);
    dump_memory();
}
echo 'final: '.number_format(memory_get_usage(), 0, '.', ',').'<br>';
flush();
?>
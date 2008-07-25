<?php
// $Rev$
error_reporting(E_ALL);
include_once('../simple_html_dom.php');
// -----------------------------------------------------------------------------

function dump_memory() {
    echo 'peak: ' . number_format(memory_get_peak_usage(), 0, '.', ',') . ' bytes, end: ' . number_format(memory_get_usage(), 0, '.', ',') . " bytes<br>";
}


// -----------------------------------------------------------------------------
$filename = './html/google.htm';

echo 'init ' . number_format(memory_get_usage(), 0, '.', ',') . " bytes";

echo '<br><br>[load file]<br>init memory: '.number_format(memory_get_usage(), 0, '.', ',').'<br>';
echo '--------------------------------------------------------------------<br>';
flush();
for($i=0; $i<3; ++$i) {
    $str = file_get_contents($filename);
    unset($str);
    dump_memory();
}
echo 'final: '.number_format(memory_get_usage(), 0, '.', ',').'<br>';
flush();


$str = file_get_contents($filename);
echo '<br><br>[multi objects str_get_html clear memory]<br>init memory: '.number_format(memory_get_usage(), 0, '.', ',').'<br>';
echo '--------------------------------------------------------------------<br>';
flush();
for($i=0; $i<3; ++$i) {
    $html = str_get_html($str);
    $html->clear();
    unset($html);
    dump_memory();
    flush();
}
echo 'final: '.number_format(memory_get_usage(), 0, '.', ',').'<br>';
flush();


echo '<br><br>[multi objects file_get_html clear memory]<br>init memory: '.number_format(memory_get_usage(), 0, '.', ',').'<br>';
echo '--------------------------------------------------------------------<br>';
flush();
for($i=0; $i<3; ++$i) {
    $html = file_get_html($filename);
    $html->clear();
    unset($html);
    dump_memory();
    flush();
}
echo 'final: '.number_format(memory_get_usage(), 0, '.', ',').'<br>';
flush();


echo '<br><br>[one object]<br>init memory: '.number_format(memory_get_usage(), 0, '.', ',').'<br>';
echo '------------------------------------------<br>';
flush();
$dom = new simple_html_dom;
for($i=0; $i<3; ++$i) {
    $html->load_file($filename);
    $html->clear();
    dump_memory();
}
unset($dom);
echo 'final: '.number_format(memory_get_usage(), 0, '.', ',').'<br>';
flush();


echo '<br><br>[multi objects without clear memory]<br>init memory: '.number_format(memory_get_usage(), 0, '.', ',').'<br>';
echo '------------------------------------------<br>';
flush();
for($i=0; $i<3; ++$i) {
    $html = file_get_html($filename);
    dump_memory();
}
echo 'final: '.number_format(memory_get_usage(), 0, '.', ',').'<br>';
flush();
?>
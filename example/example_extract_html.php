<?php
include_once('../simple_html_dom.php');

echo file_get_dom('http://www.google.com/')->plaintext;
?>
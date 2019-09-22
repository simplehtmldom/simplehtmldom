<?php
// This example illustrates how to extract text content from a webpage
include_once '../simple_html_dom.php';
echo file_get_html('https://www.google.com/')->plaintext;

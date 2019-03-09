<?php

require_once 'simple_html_dom.php';

$doc = '<div id="hello">Hello, </div><div id="world">World!</div>';

$html = str_get_html($doc);

$html->find('div', 1)->class = 'bar';
$html->find('div[id=hello]', 0)->innertext = 'foo';

echo $html;
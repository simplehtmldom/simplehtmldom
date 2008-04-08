<?php
error_reporting(E_ALL);
include('../html_dom_parser.php');


// -----------------------------------------------------------------------------
//  [attribute=value] selector
$str = <<<HTML
<input type="radio" name="newsletter" value="Hot Fuzz" />
<input type="radio" name="newsletters" value="Cold Fusion" />
<input type="radio" name="accept" value="Evil Plans" />
HTML;
$dom = str_get_dom($str);

$ret = $dom->find('[name=newsletter]');
assert(count($ret)==1);
assert($ret[0]->name=='newsletter');
assert($ret[0]->value=='Hot Fuzz');
assert($ret[0]->outertext=='<input type="radio" name="newsletter" value="Hot Fuzz" />');

$ret = $dom->find('[name="newsletter"]');
assert(count($ret)==1);
assert($ret[0]->name=='newsletter');
assert($ret[0]->value=='Hot Fuzz');
assert($ret[0]->outertext=='<input type="radio" name="newsletter" value="Hot Fuzz" />');

// -----------------------------------------------------------------------------
//  [attribute!=value] selector
$str = <<<HTML
<input type="radio" name="newsletter" value="Hot Fuzz" />
<input type="radio" name="newsletter" value="Cold Fusion" />
<input type="radio" name="accept" value="Evil Plans" />
HTML;
$dom = str_get_dom($str);

$ret = $dom->find('[name!=newsletter]');
assert(count($ret)==1);
assert($ret[0]->name=='accept');
assert($ret[0]->value=='Evil Plans');
assert($ret[0]->outertext=='<input type="radio" name="accept" value="Evil Plans" />');

$ret = $dom->find('[name!="newsletter"]');
assert(count($ret)==1);
assert($ret[0]->name=='accept');
assert($ret[0]->value=='Evil Plans');
assert($ret[0]->outertext=='<input type="radio" name="accept" value="Evil Plans" />');

$ret = $dom->find("[name!='newsletter']");
assert(count($ret)==1);
assert($ret[0]->name=='accept');
assert($ret[0]->value=='Evil Plans');
assert($ret[0]->outertext=='<input type="radio" name="accept" value="Evil Plans" />');

// -----------------------------------------------------------------------------
//  [attribute^=value] selector
$str = <<<HTML
<input name="newsletter" />
<input name="milkman" />
<input name="newsboy" />
HTML;
$dom = str_get_dom($str);

$ret = $dom->find('[name^=news]');
assert(count($ret)==2);
assert($ret[0]->name=='newsletter');
assert($ret[0]->outertext=='<input name="newsletter" />');
assert($ret[1]->name=='newsboy');
assert($ret[1]->outertext=='<input name="newsboy" />');

$ret = $dom->find('[name^="news"]');
assert(count($ret)==2);
assert($ret[0]->name=='newsletter');
assert($ret[0]->outertext=='<input name="newsletter" />');
assert($ret[1]->name=='newsboy');
assert($ret[1]->outertext=='<input name="newsboy" />');

// -----------------------------------------------------------------------------
//  [attribute$=value] selector
$str = <<<HTML
<input name="newsletter" />
<input name="milkman" />
<input name="jobletter" />
HTML;
$dom = str_get_dom($str);

$ret = $dom->find('[name$=letter]');
assert(count($ret)==2);
assert($ret[0]->name=='newsletter');
assert($ret[0]->outertext=='<input name="newsletter" />');
assert($ret[1]->name=='jobletter');
assert($ret[1]->outertext=='<input name="jobletter" />');

$ret = $dom->find('[name$="letter"]');
assert(count($ret)==2);
assert($ret[0]->name=='newsletter');
assert($ret[0]->outertext=='<input name="newsletter" />');
assert($ret[1]->name=='jobletter');
assert($ret[1]->outertext=='<input name="jobletter" />');

// -----------------------------------------------------------------------------
//  [attribute*=value] selector
$str = <<<HTML
<input name="man-news" />
<input name="milkman" />
<input name="letterman2" />
<input name="newmilk" />
HTML;
$dom = str_get_dom($str);

$ret = $dom->find('[name*=man]');
assert(count($ret)==3);
assert($ret[0]->name=='man-news');
assert($ret[0]->outertext=='<input name="man-news" />');
assert($ret[1]->name=='milkman');
assert($ret[1]->outertext=='<input name="milkman" />');
assert($ret[2]->name=='letterman2');
assert($ret[2]->outertext=='<input name="letterman2" />');

$ret = $dom->find('[name*="man"]');
assert(count($ret)==3);
assert($ret[0]->name=='man-news');
assert($ret[0]->outertext=='<input name="man-news" />');
assert($ret[1]->name=='milkman');
assert($ret[1]->outertext=='<input name="milkman" />');
assert($ret[2]->name=='letterman2');
assert($ret[2]->outertext=='<input name="letterman2" />');

// -----------------------------------------------------------------------------
// Testcase for '[]' names element
// -----------------------------------------------------------------------------
//  normal checkbox
$str = <<<HTML
<input type="checkbox" name="news" value="foo" />
<input type="checkbox" name="news" value="bar" />
<input type="checkbox" name="news" value="baz" />
HTML;
$dom = str_get_dom($str);
$ret = $dom->find('[name=news]');
assert(count($ret)==3);
assert($ret[0]->name=='news');
assert($ret[0]->value=='foo');
assert($ret[1]->name=='news');
assert($ret[1]->value=='bar');
assert($ret[2]->name=='news');
assert($ret[2]->value=='baz');

// -----------------------------------------------------------------------------
//  with '[]' names checkbox
$str = <<<HTML
<input type="checkbox" name="news[]" value="foo" />
<input type="checkbox" name="news[]" value="bar" />
<input type="checkbox" name="news[]" value="baz" />
HTML;
$dom = str_get_dom($str);
$ret = $dom->find('[name=news[]]');
assert(count($ret)==3);
assert($ret[0]->name=='news[]');
assert($ret[0]->value=='foo');
assert($ret[1]->name=='news[]');
assert($ret[1]->value=='bar');
assert($ret[2]->name=='news[]');
assert($ret[2]->value=='baz');

// -----------------------------------------------------------------------------
echo 'All pass!<br>';
?>
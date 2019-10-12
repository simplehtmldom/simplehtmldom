<?php
// This example illustrates how to use advanced selector features
include_once '../HtmlDocument.php';
use simplehtmldom\HtmlDocument;

// -----------------------------------------------------------------------------
echo '<h2>Descendant selectors</h2>' . PHP_EOL;

$doc = <<<HTML
<div>
	<div>
		<div class="foo bar">ok</div>
	</div>
</div>
HTML;

echo (new HtmlDocument())->load($doc)->find('div div div', 0)->innertext . PHP_EOL;

// -----------------------------------------------------------------------------
echo '<h2>Nested selectors</h2>' . PHP_EOL;

$doc = <<<HTML
<ul id="ul1">
	<li>item:<span>1</span></li>
	<li>item:<span>2</span></li>
</ul>
<ul id="ul2">
	<li>item:<span>3</span></li>
	<li>item:<span>4</span></li>
</ul>
HTML;

$html = (new HtmlDocument())->load($doc);

foreach($html->find('ul') as $ul) {
	foreach($ul->find('li') as $li)
		echo $li->innertext . '<br>' . PHP_EOL;
}

// -----------------------------------------------------------------------------
echo '<h2>Parsing checkboxes</h2>' . PHP_EOL;

$doc = <<<HTML
<form name="form1" method="post" action="">
	<input type="checkbox" name="checkbox1" value="checkbox1" checked>item1<br>
	<input type="checkbox" name="checkbox2" value="checkbox2">item2<br>
	<input type="checkbox" name="checkbox3" value="checkbox3" checked>item3<br>
</form>
HTML;

$html = (new HtmlDocument())->load($doc);

foreach($html->find('input[type=checkbox]') as $checkbox) {
	if ($checkbox->checked) {
		echo $checkbox->name . ' is checked<br>' . PHP_EOL;
	} else {
		echo $checkbox->name . ' is not checked<br>' . PHP_EOL;
	}
}

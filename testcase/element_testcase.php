<?php
error_reporting(E_ALL);
require_once('../html_dom_parser.php');
$dom = new html_dom_parser;

// -----------------------------------------------------------------------------
// DOM tree test
$dom->load('');
$e = $dom->root;
assert($e->first_child()==null);
assert($e->last_child()==null);
assert($e->next_sibling()==null);
assert($e->previous_sibling()==null);
// -----------------------------------------------
$str = '<div id="div1"></div>';
$dom->load($str);
$e = $dom->root;
assert($e->first_child()->id=='div1');
assert($e->last_child()->id=='div1');
assert($e->next_sibling()==null);
assert($e->previous_sibling()==null);
assert($e->plaintext=='');
assert($e->innertext==$str);
assert($e->outertext==$str);
// -----------------------------------------------
$str = <<<HTML
<div id="div1">
    <div id="div10"></div>
    <div id="div11"></div>
    <div id="div12"></div>
</div>
HTML;
$dom->load($str);

$e = $dom->find('div#div1', 0);
assert($e->first_child()->id=='div10');
assert($e->last_child()->id=='div12');
assert($e->next_sibling()==null);
assert($e->previous_sibling()==null);
// -----------------------------------------------
$str = <<<HTML
<div id="div0">
    <div id="div00"></div>
</div>
<div id="div1">
    <div id="div10"></div>
    <div id="div11"></div>
    <div id="div12"></div>
</div>
<div id="div2"></div>
HTML;
$dom->load($str);

$e = $dom->find('div#div1', 0);
assert($e->first_child()->id=='div10');
assert($e->last_child()->id=='div12');
assert($e->next_sibling()->id=='div2');
assert($e->previous_sibling()->id=='div0');

$e = $dom->find('div#div2', 0);
assert($e->first_child()==null);
assert($e->last_child()==null);

$e = $dom->find('div#div0 div#div00', 0);
assert($e->first_child()==null);
assert($e->last_child()==null);
assert($e->next_sibling()==null);
assert($e->previous_sibling()==null);

// -----------------------------------------------------------------------------
// no value attr test
$str = <<<HTML
<form name="form1" method="post" action="">
    <input type="checkbox" name="checkbox0" checked value="checkbox0">aaa<br>
    <input type="checkbox" name="checkbox1" value="checkbox1">bbb<br>
    <input type="checkbox" name="checkbox2" value="checkbox2" checked>ccc<br>
</form>
HTML;
$dom->load($str);

$counter = 0;
foreach($dom->find('input[type=checkbox]') as $checkbox) {
    if (isset($checkbox->checked)) {
        assert($checkbox->value=="checkbox$counter");
        $counter += 2;
    }
}

$counter = 0;
foreach($dom->find('input[type=checkbox]') as $checkbox) {
    if ($checkbox->checked) {
        assert($checkbox->value=="checkbox$counter");
        $counter += 2;
    }
}

$es = $dom->find('input[type=checkbox]');
$es[1]->checked = true;
assert($es[1]->outertext=='<input type="checkbox" name="checkbox1" value="checkbox1" checked>');
$es[0]->checked = false;
assert($es[0]->outertext=='<input type="checkbox" name="checkbox0"  value="checkbox0">');
$es[0]->checked = true;
assert($es[0]->outertext=='<input type="checkbox" name="checkbox0" checked value="checkbox0">');

// -----------------------------------------------------------------------------
// innertext test
$str = <<<HTML
<html>
    <head></head>
    <body>
        <br>
        <span>foo</span>
    </body>
</html>
HTML;
$dom->load($str);
assert($dom->save()==$str);
// -----------------------------------------------
$str2 = <<<HTML
<html>
    <head></head>
    <body>
        <br>
        <span>bar</span>
    </body>
</html>
HTML;
$dom->find('span', 0)->innertext = 'bar';
assert($dom->save()==$str2);
// -----------------------------------------------
$str3 = <<<HTML
<html>
    <head>ok</head>
    <body>
        <br>
        <span>bar</span>
    </body>
</html>
HTML;
$dom->find('head', 0)->innertext = 'ok';
assert($dom->save()==$str3);

// -----------------------------------------------------------------------------
// replacement test
$str = <<<HTML
<div class="class1" id="id2" ><div class="class2">ok</div></div>
HTML;
$dom->load($str);
$es = $dom->find('div');
assert(count($es)==2);
assert($es[0]->innertext=='<div class="class2">ok</div>');
assert($es[0]->outertext=='<div class="class1" id="id2" ><div class="class2">ok</div></div>');

// test isset
$es[0]->class = 'class_test';
assert(isset($es[0]->class)===true);
assert(isset($es[0]->okok)===false);

// test replacement
$es[0]->class = 'class_test';
assert($es[0]->outertext=='<div class="class_test" id="id2" ><div class="class2">ok</div></div>');

// test replacement
$es[0]->tag = 'span';
assert($es[0]->outertext=='<span class="class_test" id="id2" ><div class="class2">ok</div></span>');

// test unset (no more support...)
//$dom = str_get_dom($str);
//$es = $dom->find('div');
//unset($es[0]->class);
//assert($es[0]->outertext=='<div id="id2" ><div class="class2">ok</div></div>');

$dom->load($str);
$es = $dom->find('div');
unset($es[0]->attr['class']);
assert($es[0]->outertext=='<div id="id2" ><div class="class2">ok</div></div>');

// -----------------------------------------------------------------------------
// nested replacement test
$str = <<<HTML
<div class="class0" id="id0" ><div class="class1">ok</div></div>
HTML;
$dom->load($str);
$es = $dom->find('div');
assert(count($es)==2);
assert($es[0]->innertext=='<div class="class1">ok</div>');
assert($es[0]->outertext=='<div class="class0" id="id0" ><div class="class1">ok</div></div>');
assert($es[1]->innertext=='ok');
assert($es[1]->outertext=='<div class="class1">ok</div>');

// test replacement
$es[1]->innertext = 'okok';
assert($es[1]->outertext=='<div class="class1">okok</div>');
assert($es[0]->outertext=='<div class="class0" id="id0" ><div class="class1">okok</div></div>');
assert($dom->save()=='<div class="class0" id="id0" ><div class="class1">okok</div></div>');

$es[1]->class = 'class_test';
assert($es[1]->outertext=='<div class="class_test">okok</div>');
assert($es[0]->outertext=='<div class="class0" id="id0" ><div class="class_test">okok</div></div>');
assert($dom->save()=='<div class="class0" id="id0" ><div class="class_test">okok</div></div>');

$es[0]->class = 'class_test';
assert($es[0]->outertext=='<div class="class_test" id="id0" ><div class="class_test">okok</div></div>');
assert($dom->save()=='<div class="class_test" id="id0" ><div class="class_test">okok</div></div>');

$es[0]->innertext = 'okokok';
assert($es[0]->outertext=='<div class="class_test" id="id0" >okokok</div>');
assert($dom->save()=='<div class="class_test" id="id0" >okokok</div>');

// -----------------------------------------------------------------------------
// <p> test
$str = <<<HTML
<div class="class0">
    <p>ok0<a href="#">link0</a></p>
    <div class="class1"><p>ok1<a href="#">link1</a></p></div>
    <div class="class2"></div>
    <p>ok2<a href="#">link2</a></p>
</div>
HTML;
$dom->load($str);
$es  = $dom->find('p');
assert($es[0]->innertext=='ok0<a href="#">link0</a>');
assert($es[1]->innertext=='ok1<a href="#">link1</a>');
assert($es[2]->innertext=='ok2<a href="#">link2</a>');
assert($dom->find('p', 0)->plaintext=='ok0link0');
assert($dom->find('p', 1)->plaintext=='ok1link1');
assert($dom->find('p', 2)->plaintext=='ok2link2');

$count = 0;
foreach($dom->find('p') as $p) {
    $a = $p->find('a');
    assert($a[0]->innertext=='link'.$count);
    ++$count;
}

$es = $dom->find('p a');
assert($es[0]->innertext=='link0');
assert($es[1]->innertext=='link1');
assert($es[2]->innertext=='link2');
assert($dom->find('p a', 0)->plaintext=='link0');
assert($dom->find('p a', 1)->plaintext=='link1');
assert($dom->find('p a', 2)->plaintext=='link2');

assert($dom->save()==$str);

// -----------------------------------------------------------------------------
// <embed> test
$str = <<<HTML
<EMBED 
   SRC="../graphics/sounds/1812over.mid"
   HEIGHT=60 WIDTH=144>
HTML;
$dom->load($str);
$e = $dom->find('embed', 0);
assert($e->src=='../graphics/sounds/1812over.mid');
assert($e->height=='60');
assert($e->width=='144');
assert($dom->save()==strtolower($str));

// -----------------------------------------------------------------------------
// <pre> test
$str = <<<HTML
<div class="class0" id="id0" >
    <pre>
        <input type=submit name="btnG" value="go" onclick='goto("url0")'>
    </pre>
</div>
HTML;
$dom->load($str);
assert(count($dom->find('input'))==0);

// -----------------------------------------------------------------------------
// <code> test
$str = <<<HTML
<div class="class0" id="id0" >
    <CODE>
        <input type=submit name="btnG" value="go" onclick='goto("url0")'>
    </CODE>
</div>
HTML;
$dom->load($str);
assert(count($dom->find('code'))==1);
assert(count($dom->find('input'))==0);

// -----------------------------------------------------------------------------
$dom->clear();
unset($dom);
?>
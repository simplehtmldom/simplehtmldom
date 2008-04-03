<?php
error_reporting(E_ALL);
include('../html_dom_parser.php');

function dump_all_nodes($dom) {
    foreach ($dom->nodes as $n) {
        echo $n->nodetype.'-'.htmlspecialchars($n->text()).'<br>';
    }
}

//echo htmlspecialchars($dom->save()).'!!';
//dump_all_nodes($dom);

// -----------------------------------------------------------------------------
// empty test
$str = '';
$dom = str_get_dom($str);
assert($dom->save()==$str);

$str = null;
$dom = str_get_dom($str);
assert($dom->save()==$str);

// -----------------------------------------------------------------------------
// test endless tag
// test
$str = <<<HTML
<
HTML;
$dom = str_get_dom($str);
assert($dom->save()==$str);

// test
$str = <<<HTML
<

HTML;
$dom = str_get_dom($str);
assert($dom->save()==$str);

// test
$str = <<<HTML


<
HTML;
$dom = str_get_dom($str);
assert($dom->save()==$str);

// test
$str = <<<HTML
<a
HTML;
$dom = str_get_dom($str);
assert($dom->save()==$str);

// test
$str = <<<HTML
<<<<ab
HTML;
$dom = str_get_dom($str);
assert($dom->save()==$str);

// test
$str = <<<HTML
<<<<ab  
HTML;
$dom = str_get_dom($str);
assert($dom->save()==$str);

// test
$str = <<<HTML
<<><<>ab  
HTML;
$dom = str_get_dom($str);
assert($dom->save()==$str);

// test
$str = <<<HTML
<abc


HTML;
$dom = str_get_dom($str);
assert($dom->save()==$str);

// test
$str = <<<HTML
>
HTML;
$dom = str_get_dom($str);
assert($dom->save()==$str);

// test
$str = <<<HTML
(<1 mol%) 
HTML;
$dom = str_get_dom($str);
echo $dom->save();
assert($dom->save()==$str);
// -----------------------------------------------------------------------------
// test noise stripping
$str = <<<HTML
<script type="text/javascript" src="test.js">ss</script>
<script type="text/javascript" src="test.js"/>
<script type="text/javascript" src="test.js" />
<script type="text/javascript" src="test.js" 
/>

<script type="text/javascript" src="test.js"/>

<style>
@import url("style.css");
</style>

<script>
var foo = "bar";
</script>

<style type="text/css">
@import url("style.css");
</style>

<script type="text/javascript">
var foo = "bar";
</script >
HTML;

$dom = str_get_dom($str);
assert(count($dom->find('script'))==7);
assert(count($dom->find('style'))==2);
assert($dom->save()==$str);

// -----------------------------------------------------------------------------
// test noise 2
$str = <<<HTML
<!--
<img class="class0" id="id0" src="src0">
-->
<img class="class1" id="id1" src="src1">
<!--<img class="class2" id="id2" src="src2">
-->
HTML;

$dom = str_get_dom($str);
$ret = $dom->find('img');
assert(count($ret)==1);
assert($dom->save()==$str);

// -----------------------------------------------------------------------------
// test last emelemt not found problem
$str = <<<HTML
<img class="class0" id="id0" src="src0">
<img class="class1" id="id1" src="src1">
<img class="class2" id="id2" src="src2">
HTML;

$dom = str_get_dom($str);
$ret = $dom->find('img');
assert(count($ret)==3);
assert($ret[0]->src=='src0');
assert($ret[1]->src=='src1');
assert($ret[2]->src=='src2');
assert($ret[0]->innertext=='');
assert($ret[1]->innertext=='');
assert($ret[2]->innertext=='');
assert($ret[0]->outertext=='<img class="class0" id="id0" src="src0">');
assert($ret[1]->outertext=='<img class="class1" id="id1" src="src1">');
assert($ret[2]->outertext=='<img class="class2" id="id2" src="src2">');

assert($dom->find('img', 0)->src=='src0');
assert($dom->find('img', 1)->src=='src1');
assert($dom->find('img', 2)->src=='src2');
assert($dom->find('img', 3)===null);
assert($dom->find('img', 99)===null);

assert($dom->save()==$str);

// -----------------------------------------------------------------------------
// test <p> tag
$str = <<<HTML
<div class="class0">
    <p>ok0<a href="#">link0</a></p>
    <div class="class1"><p>ok1<a href="#">link1</a></p></div>
    <div class="class2"></div>
    <p>ok2<a href="#">link2</a></p>
</div>
HTML;

$dom = str_get_dom($str);
$ret = $dom->find('p');
assert($ret[0]->innertext=='ok0<a href="#">link0</a>');
assert($ret[1]->innertext=='ok1<a href="#">link1</a>');
assert($ret[2]->innertext=='ok2<a href="#">link2</a>');
assert($dom->find('p', 0)->plaintext=='ok0link0');
assert($dom->find('p', 1)->plaintext=='ok1link1');
assert($dom->find('p', 2)->plaintext=='ok2link2');

$count = 0;
foreach($dom->find('p') as $p) {
    $a = $p->find('a');
    assert($a[0]->innertext=='link'.$count);
    ++$count;
}

$ret = $dom->find('p a');
assert($ret[0]->innertext=='link0');
assert($ret[1]->innertext=='link1');
assert($ret[2]->innertext=='link2');
assert($dom->find('p a', 0)->plaintext=='link0');
assert($dom->find('p a', 1)->plaintext=='link1');
assert($dom->find('p a', 2)->plaintext=='link2');

assert($dom->save()==$str);

// -----------------------------------------------------------------------------
// test error tag
$str = <<<HTML
    <img class="class0" id="id0" src="src0">
    <p>p1</p>
    <img class="class1" id="id1" src="src1">
    <p>
    <img class="class2" id="id2" src="src2">
    </a>
</div>
HTML;

$dom = str_get_dom($str);
$ret = $dom->find('img');
assert(count($ret)==3);
assert($ret[0]->src=='src0');
assert($ret[1]->src=='src1');
assert($ret[2]->src=='src2');

$ret = $dom->find('p');
assert($ret[0]->innertext=='p1');

assert($dom->save()==$str);

// -----------------------------------------------------------------------------
// innertext test 1
$str = <<<HTML
<html>
    <head></head>
    <body>
        <br>
        <span>foo</span>
    </body>
</html>
HTML;

$str2 = <<<HTML
<html>
    <head></head>
    <body>
        <br>
        <span>bar</span>
    </body>
</html>
HTML;

$dom = str_get_dom($str);
assert($dom->save()==$str);
$dom->find('span', 0)->innertext = 'bar';
assert($dom->save()==$str2);

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
// selector test 1
$str = <<<HTML
<img class="class0" id="id0" src="src0">
<img class="class1" id="id1" src="src1">
<img class="class2" id="id2" src="src2">
HTML;

$dom = str_get_dom($str);

// class
$ret = $dom->find('img.class0');
assert(count($ret)==1);
assert($ret[0]->src=='src0');
assert($ret[0]->innertext=='');
assert($ret[0]->outertext=='<img class="class0" id="id0" src="src0">');

$ret = $dom->find('.class0');
assert(count($ret)==1);
assert($ret[0]->src=='src0');
assert($ret[0]->innertext=='');
assert($ret[0]->outertext=='<img class="class0" id="id0" src="src0">');

// id
$ret = $dom->find('img#id1');
assert(count($ret)==1);
assert($ret[0]->src=='src1');
assert($ret[0]->innertext=='');
assert($ret[0]->outertext=='<img class="class1" id="id1" src="src1">');

$ret = $dom->find('#id2');
assert(count($ret)==1);
assert($ret[0]->src=='src2');
assert($ret[0]->innertext=='');
assert($ret[0]->outertext=='<img class="class2" id="id2" src="src2">');

// attr
$ret = $dom->find('img[src="src0"]');
assert(count($ret)==1);
assert($ret[0]->src=='src0');
assert($ret[0]->innertext=='');
assert($ret[0]->outertext=='<img class="class0" id="id0" src="src0">');

$ret = $dom->find('img[src=src0]');
assert(count($ret)==1);
assert($ret[0]->src=='src0');
assert($ret[0]->innertext=='');
assert($ret[0]->outertext=='<img class="class0" id="id0" src="src0">');

$ret = $dom->find('[src=src0]');
assert(count($ret)==1);
assert($ret[0]->src=='src0');
assert($ret[0]->innertext=='');
assert($ret[0]->outertext=='<img class="class0" id="id0" src="src0">');

$ret = $dom->find('[src="src0"]');
assert(count($ret)==1);
assert($ret[0]->src=='src0');
assert($ret[0]->innertext=='');
assert($ret[0]->outertext=='<img class="class0" id="id0" src="src0">');

// -----------------------------------------------------------------------------
// multiple selector test
$str = <<<HTML
<div class="class0" id="id0" ><div class="class1" id="id1"><div class="class2" id="id2">ok</div></div></div>
HTML;

$dom = str_get_dom($str);

$ret = $dom->find('div');
assert(count($ret)==3);
assert($ret[0]->id=='id0');
assert($ret[1]->id=='id1');
assert($ret[2]->id=='id2');

$ret = $dom->find('div div');
assert(count($ret)==2);
assert($ret[0]->id=='id1');
assert($ret[1]->id=='id2');

$ret = $dom->find('div div div');
assert(count($ret)==1);
assert($ret[0]->id=='id2');

// -----------------------------------------------------------------------------
// multiple selector test 2
$str = <<<HTML
<table>
    <tr>
        <td>0</td>
        <td>1</td>
    </tr>
</table>
<table>
    <tr>
        <td>2</td>
        <td>3</td>
    </tr>
</table>
HTML;

$dom = str_get_dom($str);

$ret = $dom->find('table td');
assert(count($ret)==4);
assert($ret[0]->innertext=='0');
assert($ret[1]->innertext=='1');
assert($ret[2]->innertext=='2');
assert($ret[3]->innertext=='3');

// -----------------------------------------------------------------------------
// multiple selector test 3
$str = <<<HTML
<table>
    <tr>
        <td>
            <table class="hello">
                <tr>
                    <td>0</td>
                    <td>1</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<table class="hello">
    <tr>
        <td>2</td>
        <td>3</td>
    </tr>
</table>
HTML;

$dom = str_get_dom($str);

$ret = $dom->find('table.hello td');
assert(count($ret)==4);
assert($ret[0]->innertext=='0');
assert($ret[1]->innertext=='1');
assert($ret[2]->innertext=='2');
assert($ret[3]->innertext=='3');


// -----------------------------------------------------------------------------
// test nested selector
$str = <<<HTML
<ul>
    <li>0</li>
    <li>1</li>
</ul>
<ul>
    <li>2</li>
    <li>3</li>
</ul>
HTML;

$dom = str_get_dom($str);
$ret = $dom->find('ul');
assert(count($ret)==2);

foreach($ret as $n) {
    $li = $n->find('li');
    assert(count($li)==2);
}

$ret = $dom->find('li');
assert(count($ret)==4);
assert($ret[0]->innertext=='0');
assert($ret[1]->innertext=='1');
assert($ret[2]->innertext=='2');
assert($ret[3]->innertext=='3');
assert($ret[0]->outertext=='<li>0</li>');
assert($ret[1]->outertext=='<li>1</li>');
assert($ret[2]->outertext=='<li>2</li>');
assert($ret[3]->outertext=='<li>3</li>');

$counter = 0;
foreach($dom->find('ul') as $ul) {
    foreach($ul->find('li') as $li) {
        assert($li->innertext=="$counter");
            assert($li->outertext=="<li>$counter</li>");
        ++$counter;
    }
}

// -----------------------------------------------------------------------------
// test no value attr selector
$str = <<<HTML
<form name="form1" method="post" action="">
    <input type="checkbox" name="checkbox0" checked value="checkbox0">aaa<br>
    <input type="checkbox" name="checkbox1" value="checkbox1">bbb<br>
    <input type="checkbox" name="checkbox2" value="checkbox2" checked>ccc<br>
</form>
HTML;

$dom = str_get_dom($str);

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

$ret = $dom->find('input[type=checkbox]');
$ret[1]->checked = true;
assert($ret[1]->outertext=='<input type="checkbox" name="checkbox1" value="checkbox1" checked>');
$ret[0]->checked = false;
assert($ret[0]->outertext=='<input type="checkbox" name="checkbox0"  value="checkbox0">');
$ret[0]->checked = true;
assert($ret[0]->outertext=='<input type="checkbox" name="checkbox0" checked value="checkbox0">');

// -----------------------------------------------------------------------------
// test replacement
$str = <<<HTML
<div class="class1" id="id2" ><div class="class2">ok</div></div>
HTML;

$dom = str_get_dom($str);
$ret = $dom->find('div');
assert(count($ret)==2);
assert($ret[0]->innertext=='<div class="class2">ok</div>');
assert($ret[0]->outertext=='<div class="class1" id="id2" ><div class="class2">ok</div></div>');

// test isset
$ret[0]->class = 'class_test';
assert(isset($ret[0]->class)===true);
assert(isset($ret[0]->okok)===false);

// test replacement
$ret[0]->class = 'class_test';
assert($ret[0]->outertext=='<div class="class_test" id="id2" ><div class="class2">ok</div></div>');

// test replacement
$ret[0]->tag = 'span';
assert($ret[0]->outertext=='<span class="class_test" id="id2" ><div class="class2">ok</div></span>');

// test unset (no more support...)
//$dom = str_get_dom($str);
//$ret = $dom->find('div');
//unset($ret[0]->class);
//assert($ret[0]->outertext=='<div id="id2" ><div class="class2">ok</div></div>');
$dom = str_get_dom($str);
$ret = $dom->find('div');
unset($ret[0]->attr['class']);
assert($ret[0]->outertext=='<div id="id2" ><div class="class2">ok</div></div>');

// -----------------------------------------------------------------------------
// test nested replacement
$str = <<<HTML
<div class="class0" id="id0" ><div class="class1">ok</div></div>
HTML;

$dom = str_get_dom($str);
$ret = $dom->find('div');
assert(count($ret)==2);
assert($ret[0]->innertext=='<div class="class1">ok</div>');
assert($ret[0]->outertext=='<div class="class0" id="id0" ><div class="class1">ok</div></div>');
assert($ret[1]->innertext=='ok');
assert($ret[1]->outertext=='<div class="class1">ok</div>');

// test replacement
$ret[1]->innertext = 'okok';
assert($ret[1]->outertext=='<div class="class1">okok</div>');
assert($ret[0]->outertext=='<div class="class0" id="id0" ><div class="class1">okok</div></div>');
assert($dom->save()=='<div class="class0" id="id0" ><div class="class1">okok</div></div>');

$ret[1]->class = 'class_test';
assert($ret[1]->outertext=='<div class="class_test">okok</div>');
assert($ret[0]->outertext=='<div class="class0" id="id0" ><div class="class_test">okok</div></div>');
assert($dom->save()=='<div class="class0" id="id0" ><div class="class_test">okok</div></div>');

$ret[0]->class = 'class_test';
assert($ret[0]->outertext=='<div class="class_test" id="id0" ><div class="class_test">okok</div></div>');
assert($dom->save()=='<div class="class_test" id="id0" ><div class="class_test">okok</div></div>');

$ret[0]->innertext = 'okokok';
assert($ret[0]->outertext=='<div class="class_test" id="id0" >okokok</div>');
assert($dom->save()=='<div class="class_test" id="id0" >okokok</div>');

// -----------------------------------------------------------------------------
// test text
$str = <<<HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
                      "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"></html>
HTML;

$dom = str_get_dom($str);
$ret = $dom->find('text');
assert(count($ret)==2);

// -----------------------------------------------------------------------------
// test string quote
$str = <<<HTML
<div class="class0" id="id0" >
    okok<br>
    <input type=submit name="btnG" value="go" onclick='goto("url0")'>
    <br/>
    <div><input type=submit name="btnG2" value="go" onclick="goto('url1'+'\'')"/></div>
    <input type=submit name="btnG2" value="go" onclick="goto('url2')"/>
    <div><input type=submit name="btnG2" value="go" onclick='goto("url4"+"\"")'></div>
    <br/>
</div>
HTML;

$dom = str_get_dom($str);
$ret = $dom->find('input');
assert(count($ret)==4);
assert($ret[0]->onclick=='goto("url0")');
assert($ret[1]->onclick=="goto('url1'+'\'')");
assert($ret[2]->onclick=="goto('url2')");
assert($ret[3]->onclick=='goto("url4"+"\"")');

// -----------------------------------------------------------------------------
// test comments
$str = <<<HTML
<div class="class0" id="id0" >
    <!--
        <input type=submit name="btnG" value="go" onclick='goto("url0")'>
    -->
</div>
HTML;

$dom = str_get_dom($str);
$ret = $dom->find('input');
assert(count($ret)==0);

// -----------------------------------------------------------------------------
// test pre
$str = <<<HTML
<div class="class0" id="id0" >
    <pre>
        <input type=submit name="btnG" value="go" onclick='goto("url0")'>
    </pre>
</div>
HTML;

$dom = str_get_dom($str);
$ret = $dom->find('input');
assert(count($ret)==0);

// -----------------------------------------------------------------------------
// test invalid 1
$str = <<<HTML
<div>
    <div class="class0" id="id0" >
    <img class="class0" id="id0" src="src0">
    </img>
    <img class="class0" id="id0" src="src0">
    </div>
</div>
HTML;

$dom = str_get_dom($str);
$ret = $dom->find('img');
assert(count($ret)==2);
$ret = $dom->find('div');
assert(count($ret)==2);

// -----------------------------------------------------------------------------
// test invalid 2
$str = <<<HTML
<div>
    <div class="class0" id="id0" >
    <span></span>
    </span>
    <span></span>
    </div>
</div>
HTML;

$dom = str_get_dom($str);
$ret = $dom->find('span');
assert(count($ret)==2);
$ret = $dom->find('div');
assert(count($ret)==2);

// -----------------------------------------------------------------------------
// test invalid 3
$str = <<<HTML
<div>
    <div class="class0" id="id0" >
    <span></span>
    <span>
    <span></span>
    </div>
</div>
HTML;

$dom = str_get_dom($str);
$ret = $dom->find('span');
assert(count($ret)==3);
$ret = $dom->find('div');
assert(count($ret)==2);


// -----------------------------------------------------------------------------
// test customize parsing
$str = <<<HTML
<script type="text/javascript" src="test.js">ss</script>
HTML;

$my_dom = new html_dom_parser;
$my_dom->prepare($str);
$count = 0;
while ($node=$dom->parse()) {
    switch ($count) {
        case 0: assert($node->nodetype==HDOM_TYPE_ELEMENT); break;
        case 1: assert($node->nodetype==HDOM_TYPE_TEXT); assert($node->text()==='ss'); break;
        case 2: assert($node->nodetype==HDOM_TYPE_ENDTAG); break;
    }
    ++$count;
}

$my_dom->prepare($str);
// strip out <script> tags
$my_dom->remove_noise("'<\s*script[^>]*[^/]>(.*?)<\s*/\s*script\s*>'is", false, false);
$my_dom->remove_noise("'<\s*script\s*>(.*?)<\s*/\s*script\s*>'is", false, false);
$count = 0;
while ($node=$dom->parse()) {
    switch ($count) {
        case 0: assert($node->nodetype==HDOM_TYPE_ELEMENT); break;
        case 1: assert($node->nodetype==HDOM_TYPE_TEXT); assert($node->text()===''); break;
        case 2: assert($node->nodetype==HDOM_TYPE_ENDTAG); break;
    }
    ++$count;
}


// -----------------------------------------------------------------------------
echo 'All pass!<br>';
?>


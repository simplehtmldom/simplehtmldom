<?php
include_once('../simple_html_dom.php');

// -----------------------------------------------------------------------------
// remove HTML comments
function html_no_comment($url) {
    // create DOM
    $dom = file_get_dom($url);

    // remove all comment elements
    foreach($dom->find('comment') as $e)
        $e->outertext = '';

    $ret = $dom->save();

    // clean up memory
    $dom->clear();
    unset($dom);

    return $ret;
}

// -----------------------------------------------------------------------------
// test it!
echo html_no_comment('http://www.google.com/');


// -----------------------------------------------------------------------------
// search elements that contains an especific text
function find_contains($dom, $selector, $keyword, $index=-1) {
    $ret = array();
    foreach ($dom->find($selector) as $e) {
        if (strpos($e->innertext, $keyword)!==false)
            $ret[] = $e;
    }

    if ($index<0) return $ret;
    return (isset($ret[$index])) ? $ret[$index] : null;
}

// -----------------------------------------------------------------------------
// test it!
$dom = file_get_dom('http://www.google.com/');
foreach(find_contains($dom, "a", "Google") as $e)
    echo $e->outertext."<BR>";
?>
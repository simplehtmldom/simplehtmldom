<?php
include_once('../simple_html_dom.php');

// -----------------------------------------------------------------------------
// remove HTML comments
function html_no_comment($str) {
    // create DOM
    $dom = str_get_dom($str);

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
// extract HTML contents
function html_to_text($str) {
    // 1. create DOM object
    $parser = new simple_html_dom;

    // 2. prepare HTML data and init everything
    $parser->prepare($str, false);

    // 3. some contents such as 'comments', 'styles' or 'script' will be treated as 'text',
    // so we need to remove it before parsing...

    // strip out DOCTYPE
    $parser->remove_noise("'<!doctype(.*?)>'is");
    // strip out comments
    $parser->remove_noise("'<!--(.*?)-->'is");
    // strip out <style> tags
    $parser->remove_noise("'<\s*style[^>]*[^/]>(.*?)<\s*/\s*style\s*>'is", false);
    $parser->remove_noise("'<\s*style\s*>(.*?)<\s*/\s*style\s*>'is", false);
    // strip out <script> tags
    $parser->remove_noise("'<\s*script[^>]*[^/]>(.*?)<\s*/\s*script\s*>'is", false);
    $parser->remove_noise("'<\s*script\s*>(.*?)<\s*/\s*script\s*>'is", false);
    // strip out <pre> tags
    $parser->remove_noise("'<\s*pre[^>]*>(.*?)<\s*/\s*pre\s*>'is", false, false);
    // strip out <code> tags
    $parser->remove_noise("'<\s*code[^>]*>(.*?)<\s*/\s*code\s*>'is", false, false);

    // 4. parsing each node
    $ret = '';
    while ($node=$parser->parse()) {
        // dump node's contents which tag is 'text'
        if ($node->nodetype==HDOM_TYPE_TEXT) {
            $text = $node->text();

            // skip some BAD-HTML-mis-match block tags
            if (strpos($text, '</')!==false)
                continue;

            $ret .= htmlspecialchars_decode($text);
        }
    }

    // clean up memory
    $parser->clear();
    unset($parser);

    return $ret;
}

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
<?
// example of how to customize the parsing behavior
include('../html_dom_parser.php');

// HTML text extractor
function html_extract_contents($str) {
    // 1. create DOM object
    $dom = new html_dom_parser;

    // 2. prepare HTML data and init everything
    $dom->prepare($str);

    // 3. some contents such as 'comments', 'styles' or 'script' will be treated as 'text',
    // so we need to remove it before parsing...

    // strip out DOCTYPE
    $dom->remove_noise("'<!doctype(.*?)>'is");
    // strip out comments
    $dom->remove_noise("'<!--(.*?)-->'is");
    // strip out <styles> tags
    $dom->remove_noise("'<\s*style[^>]*?>(.*?)<\s*/\s*style\s*>'is");
    // strip out <script> tags
    $dom->remove_noise("'<\s*script[^>]*?>(.*?)<\s*/\s*script\s*>'is");
    // strip out <pre> tags
    $dom->remove_noise("'<\s*pre[^>]*?>(.*?)<\s*/\s*pre\s*>'is", false, false);

    // 4. parsing each node
    $ret = '';
    while ($node=$dom->parse()) {
        // dump node's contents which type is 'text'
        if ($node->tag=='text')
            $ret .= htmlspecialchars_decode($node->text());
    }

    return $ret;
}

// test it!
$str = file_get_contents('http://www.google.com/');
echo html_extract_contents($str);
?>
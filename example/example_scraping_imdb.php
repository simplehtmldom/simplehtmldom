<?php
include_once('../html_dom_parser.php');

function scraping_IMDB($url) {
    // create DOM
    $dom = file_get_dom($url);

    // get title
    $ret['Title'] = $dom->find('title', 0)->innertext;

    // get rating
    $ret['Rating'] = $dom->find('div[class="general rating"] b', 0)->innertext;

    // get overview
    foreach($dom->find('div[class="info"]') as $div) {
        // skip user comments
        if($div->find('h5', 0)->innertext=='User Comments:')
            return $ret;

        $key = '';
        $val = '';

        foreach($div->find('*') as $node) {
            if ($node->tag=='h5')
                $key = $node->plaintext;

            if ($node->tag=='a' && $node->plaintext!='more')
                $val .= trim(str_replace("\n", '', $node->plaintext));

            if ($node->tag=='text')
                $val .= trim(str_replace("\n", '', $node->plaintext));
        }

        $ret[$key] = $val;
    }

    return $ret;
}


// test it!
// -----------------------------------------------------------------------------
$ret = scraping_IMDB('http://imdb.com/title/tt0335266/');

foreach($ret as $k=>$v)
    echo '<strong>'.$k.' </strong>'.$v.'<br>';
?>
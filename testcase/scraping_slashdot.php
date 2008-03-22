<?
include_once('../html_dom_parser.php');

function scraping_slashdot($url) {
    // create DOM
    $dom = file_get_dom($url);

    // get article block
    foreach($dom->find('div.article') as $article) {
        // get title
        $item['title'] = trim($article->find('div.title', 0)->plaintext);
        // get details
        $item['details'] = trim($article->find('div.details', 0)->plaintext);
        // get intro
        $item['intro'] = trim($article->find('div.intro', 0)->plaintext);

        $ret[] = $item;
    }

    return $ret;
}


// test it!
// -----------------------------------------------------------------------------
$ret = scraping_slashdot('http://slashdot.org/');

foreach($ret as $v) {
    echo $v['title'].'<br>';
    echo '<ul>';
    echo '<li>'.$v['details'].'</li>';
    echo '<li>'.$v['intro'].'</li>';
    echo '</ul>';
}
?>
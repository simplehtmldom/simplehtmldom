<?php
include_once('../html_dom_parser.php');

function scraping_digg($url) {
    // create DOM
    $dom = file_get_dom($url);

    // get news block
    foreach($dom->find('div.news-summary') as $article) {
        // get title
        $item['title'] = trim($article->find('h3', 0)->plaintext);
        // get details
        $item['details'] = trim($article->find('p', 0)->plaintext);
        // get intro
        $item['diggs'] = trim($article->find('li a strong', 0)->plaintext);

        $ret[] = $item;
    }

    return $ret;
}


// test it!
// -----------------------------------------------------------------------------
// "http://digg.com" will check user_agent header...
ini_set('user_agent', 'My-Application/2.5');

$ret = scraping_digg('http://digg.com/');

foreach($ret as $v) {
    echo $v['title'].'<br>';
    echo '<ul>';
    echo '<li>'.$v['details'].'</li>';
    echo '<li>Diggs: '.$v['diggs'].'</li>';
    echo '</ul>';
}

?>
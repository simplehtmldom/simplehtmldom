<?php
/**
 * This example loads a page from IMDb and displays the most important details
 * in a custom format.
 */
include_once '../../simple_html_dom.php';

// (optional) Use a custom user agent for your application
ini_set('user_agent', 'simplehtmldom_examples/1.0');

// Load the page into memory
$html = file_get_html('https://imdb.com/title/tt0335266/');

// Extract details
$title = $html->find('title', 0)->plaintext;
$rating = $html->find('div[class="ratingValue"] span', 0)->plaintext;
$storyline = $html->find('#titleStoryLine p', 0)->plaintext;

// Clean up memory
$html->clear();
unset($html);

echo '<h1>' . $title . '</h1><p>Rating: ' . $rating . '<br>' . $storyline . '</p>';

<?php
error_reporting(E_ALL);

$dir = new DirectoryIterator(getcwd());
$dir->rewind();
foreach ($dir as $entry) {
    if ($entry->isFile() && strpos($entry, '_testcase.')>0)
        require_once($entry);
}
?>
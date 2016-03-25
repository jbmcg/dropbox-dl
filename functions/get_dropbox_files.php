<?php

function get_dropbox_files($url, &$files, $recursive = true, $validExtensions = [], $folder = '/')
{
    println('Checking %s', $url);
    $contents = file_get_contents($url);
    /** @noinspection PhpUndefinedClassInspection */
    $doc = phpQuery::newDocumentHTML($contents);
    foreach ($doc->find('div.filename a') as $a) {
        $link = preg_replace('/\?.*/', '', pq($a)->attr('href'));
        $ext = pathinfo(basename($link), PATHINFO_EXTENSION);
        if (empty($ext)) {
            if ($recursive) {
                get_dropbox_files($link, $files, $recursive, $validExtensions, sprintf('%s%s/', $folder, basename(urldecode($link))));
            }
        } else {
            $add = $link . '?dl=1';
            if (count($validExtensions) > 0) {
                if (in_array(strtolower($ext), $validExtensions)) {
                    $files[$folder][] = $add;
                }
            } else {
                $files[$folder][] = $add;
            }
        }
    }
}

<?php

function get_dropbox_files($url, &$files, $recursive = true, $validExtensions = array(), $folder = '/')
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
                get_dropbox_files($link, $files, $recursive, $validExtensions,
                    sprintf('%s%s/', $folder, basename(urldecode($link))));
            }
        } else {
            $add = $link . '?dl=1';
            if (!array_key_exists($folder, $files)) {
                $files[$folder] = array();
            }
            if (count($validExtensions) > 0) {
                if (in_array(strtolower($ext), $validExtensions) && !in_array($add, $files[$folder])) {
                    $files[$folder][] = $add;
                }
            } elseif (!in_array($add, $files[$folder])) {
                $files[$folder][] = $add;
            }
        }
    }
}

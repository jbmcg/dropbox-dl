<?php

function get_dropbox_files($url, &$files, $recursive = true, $validExtensions = array(), $folder = '/')
{
    println('Checking %s', $url);
    $contents = file_get_contents($url);
    $search = '.responseReceived("';
    $start = strstr($contents, $search);
    list($line) = explode(PHP_EOL, $start);
    $jsonStart = strpos($line, $search) + strlen($search);
    $jsonEnd = strrpos($line, '")');
    $json = substr($line, $jsonStart, $jsonEnd - $jsonStart);
    $jsonStr = json_decode('"' . $json . '"', true);
    $result = json_decode($jsonStr, true);

    if (array_key_exists('entries', $result)) {
        foreach ($result['entries'] as $entry) {
            $isDir = filter_var($entry['is_dir'], FILTER_VALIDATE_BOOLEAN);
            if ($recursive && $isDir) {
                get_dropbox_files($entry['href'], $files, $recursive, $validExtensions,
                    sprintf('/%s/', basename(strtok($entry['href'], '?'))));
            } elseif (!$isDir) {
                $add = replace_query_params($entry['href'], ['dl' => 1]);
                if (!array_key_exists($folder, $files)) {
                    $files[$folder] = array();
                }
                if (count($validExtensions) > 0) {
                    $ext = pathinfo(strtok($add, '?'), PATHINFO_EXTENSION);
                    if (in_array(strtolower($ext), $validExtensions) && !in_array($add, $files[$folder])) {
                        $files[$folder][] = $add;
                    }
                } elseif (!in_array($add, $files[$folder])) {
                    $files[$folder][] = $add;
                }
            }
        }
    }
}

function replace_query_params($url, $params)
{
    $query = parse_url($url, PHP_URL_QUERY);
    parse_str($query, $oldParams);

    if (empty($oldParams)) {
        return rtrim($url, '?') . '?' . http_build_query($params);
    }

    $params = array_merge($oldParams, $params);

    return preg_replace('#\?.*#', '?' . http_build_query($params), $url);
}

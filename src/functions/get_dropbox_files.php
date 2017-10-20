<?php

function get_dropbox_files($url, &$files, $recursive = true, $validExtensions = array(), $folder = '/')
{
    println('Checking %s', $url);
    $contents = file_get_contents($url);
    $start = strstr($contents, 'InitReact.mountComponent(');
    list($line) = explode(PHP_EOL, $start);
    $lineLength = strlen($line);
    $jsonStart = strpos($line, '{');
    $jsonEnd = strrpos($line, '},');
    $json = substr($line, $jsonStart, $jsonEnd - $jsonStart + 1) . '}';
    $result = json_decode($json, true);
    $data = $result['props']['contents'];

    if ($recursive && array_key_exists('folders', $data)) {
        foreach ($data['folders'] as $folderObj) {
            get_dropbox_files($folderObj['href'], $files, $recursive, $validExtensions,
                sprintf('/%s/', basename(strtok($folderObj['href'], '?'))));
        }
    }

    if (array_key_exists('files', $data)) {
        foreach ($data['files'] as $fileObj) {
            $add = replace_query_params($fileObj['href'], ['dl' => 1]);
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

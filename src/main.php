#!/usr/bin/env php
<?php
error_reporting(E_ALL ^ E_STRICT);

require_once __DIR__ . '/vendor/autoload.php';

if (empty($argv[1])) {
    println();
    println('Usage: ');
    println();
    println('dropbox-dl [url] [path] [recursive] [ext1] [ext2] ...');
    println();
    println('url: A public Dropbox URL.');
    println('path: Local path to save files to. Defaults to current working directory (%s).', getcwd());
    println('recursive: Whether to download all files recursively (specify 1 or 0). Defaults to 1.');
    println('ext(s): Specify one or more file extensions to filter by. Add multiple parameters to accept multiple extensions.');
    println();
    exit();
} else {
    $url = $argv[1];
    if (filter_var($url, FILTER_VALIDATE_URL) === false) {
        println('ERROR: "%s" is not a valid URL.', $argv[1]);
        exit();
    }
    $dir = (isset($argv[2])) ? realpath($argv[2]) : getcwd();
    if ($dir === false || !is_dir($dir)) {
        println('ERROR: "%s" is not a real directory.', $argv[2]);
        exit();
    }
    $recursive = isset($argv[3]) ? filter_var($argv[3], FILTER_VALIDATE_BOOLEAN) : true;
    $validExtensions = array();
    foreach ($argv as $key => $value) {
        if ($key > 3) {
            $validExtensions[] = strtolower($value); // Extensions are compared in lowercase
        }
    }
}

$files = array();
get_dropbox_files($url, $files, $recursive, $validExtensions);

foreach ($files as $folder => $folderList) {

    $destFolder = $dir . $folder;
    if (!is_dir($destFolder) && count($folderList) > 0) {
        println('Creating %s', $destFolder);
        mkdir($destFolder, 0755, true);
    }

    foreach ($folderList as $fileUrl) {
        $filename = basename(preg_replace('/\?.*/', '', urldecode($fileUrl)));
        $destPath = $destFolder . $filename;

        println('Downloading %s', $fileUrl);

        $attempts = 3;
        while ($attempts > 0) {
            copy($fileUrl, $destPath);
            if (file_exists($destPath)) {
                break;
            } else {
                $attempts--;

                println('Retrying download of %s', $fileUrl);
            }
        }

        if ($attempts <= 0) {

            println('Failed to download %s', $fileUrl);
        }
    }
}

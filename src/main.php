#!/usr/bin/env php
<?php
require_once __DIR__ . '/../vendor/autoload.php';

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
    $dir = (isset($argv[2])) ? realpath($argv[2]) : getcwd();
    if ($dir === false || !is_dir($dir)) {
        println('"%s" is not a real directory.', $argv[2]);
        exit();
    }
    $recursive = isset($argv[3]) ? filter_var($argv[3], FILTER_VALIDATE_BOOLEAN) : true;
    $validExtensions = [];
    foreach ($argv as $key => $value) {
        if ($key > 3) {
            $validExtensions[] = strtolower($value); // Extensions are compared in lowercase
        }
    }
}

$files = [];
get_dropbox_files($url, $files, $recursive, $validExtensions);

foreach ($files as $folder => $folderList) {

    $destFolder = $dir . $folder;
    if (!is_dir($destFolder)) {
        println('Creating %s', $destFolder);
        mkdir($destFolder, 0755, true);
    }

    foreach ($folderList as $fileUrl) {
        println('Downloading %s', $fileUrl);
        $destPath = $destFolder . basename(preg_replace('/\?.*/', '', urldecode($fileUrl)));
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
            println('Aborting mission...');
            exit();
        }
    }
}

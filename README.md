## About
**dropbox-dl** is a small command-line program written in PHP to recursively download the contents of a public Dropbox folder (no credentials or API key/secret required). This is a bit of a hack / workaround for Dropbox not allowing you to directly download a folder that is over 1 GB in size. See the unfortunate scenario below.

![Alt text](/assets/img/sad.jpg "The zip file is too large. Please add it to your Dropbox.")

## Usage
```sh
dropbox-dl [url] [path] [recursive] [ext1] [ext2]...
```
| parameter  |  description |
|---|---|
| url  |  A public Dropbox URL. |
| path  | Local path to save files to. Defaults to current working directory.  |
| recursive  | Whether to download all files recursively (specify 1 or 0). Defaults to 1.  |
| ext(s)  | Specify one or more file extensions to filter by. Add multiple parameters to accept multiple extensions. |

## Examples

Download all files (including subfolders) to your current working directory:
```sh
dropbox-dl https://www.dropbox.com/sh/9d3gqhtqw0kf9d7/AAC09AQRODS_F7EVYXt5mgcMa
```
Download files on root of URL (no subfolders) to an existing folder on your desktop called "stuff":
```sh
dropbox-dl https://www.dropbox.com/sh/9d3gqhtqw0kf9d7/AAC09AQRODS_F7EVYXt5mgcMa ~/Desktop/stuff 0
```
Download only JPG, PNG, and GIF images (including those found in subfolders) to your current working directory:
```sh
dropbox-dl https://www.dropbox.com/sh/9d3gqhtqw0kf9d7/AAC09AQRODS_F7EVYXt5mgcMa . 1 jpg png gif
```

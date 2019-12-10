<?php
namespace Wesleywmd\Invent\Helper;

class FileHelper
{
    public function fileExists($location)
    {
        return (bool) is_file($location);
    }

    public function saveFile($location, $contents, $force = false)
    {
        if (is_file($location) && !$force) {
            throw new \Exception('File already exists at: '.$location);
        }
        $dirname = dirname($location);
        if (!is_dir($dirname)) {
            mkdir($dirname, 0777, true);
        }
        file_put_contents($location, $contents, LOCK_EX);
    }
}
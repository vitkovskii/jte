<?php
namespace Jte\Source;

class FileSource
{
    private $baseDir;

    function __construct($baseDir)
    {
        $this->baseDir = $baseDir;
    }

    public function get($templateName)
    {
        return file_get_contents($this->baseDir . '/' . $templateName);
    }
}
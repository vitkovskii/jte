<?php
namespace Jte\Other;

class Cache
{
    protected $cacheDir;
    protected $fileMode;
    protected $dirMode;

    public function __construct($cacheDir, $fileMode = 0777, $dirMode = 0777)
    {
        $this->cacheDir = $cacheDir;
        $this->fileMode = $fileMode;
        $this->dirMode = $dirMode;

        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, $this->dirMode, true);
            chmod($cacheDir, $this->dirMode);
        }
    }

    public function isCacheExists($fileName)
    {
        return file_exists($this->getFilePath($fileName));
    }

    public function save($fileName, $content)
    {
        $fileName = $this->getFilePath($fileName);
        $dir = dirname($fileName);
        if (!is_dir($dir)) {
            mkdir($dir, $this->dirMode, true);
            chmod($dir, $this->dirMode);
        }

        file_put_contents($fileName, $content);
        chmod($fileName, $this->fileMode);
    }

    public function getFilePath($fileName)
    {
        return $this->cacheDir . $fileName;
    }
}
<?php
namespace Jte\LogicProcessor;

use Jte\Cache;

class RequireLogicProcessor extends LogicProcessor
{
    /** @var  Cache */
    protected $cache;

    public function __construct($cache)
    {
        $this->cache = $cache;
    }

    protected function getValidFileSystemName($fileName)
    {
        return md5($fileName);
    }

    public function processBootLogic($template, $params)
    {
        $fileName = $this->getValidFileSystemName($template->getName()) . '/boot.php';
        if (!$this->cache->isCacheExists($fileName)) {
            $this->cache->save(
                $fileName,
                '<?php ' . $this->getLogicText($template->getBoot(), 'Jte\Language\BootLogic')
            );
        }

        return require $this->cache->getFilePath($fileName);
    }

    public function processBlockLogic($template, $blockName, $params)
    {
        $fileName = $this->getValidFileSystemName($template->getName()) . '/' . $this->getValidFileSystemName($blockName) . '.php';
        if (!$this->cache->isCacheExists($fileName)) {
            $this->cache->save(
                $fileName,
                '<?php ' . $this->getLogicText($template->getBlockLogic($blockName), 'Jte\Language\BlockLogic')
            );
        }

        return require $this->cache->getFilePath($fileName);
    }
}
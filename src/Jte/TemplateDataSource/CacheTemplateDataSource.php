<?php
namespace Jte\TemplateDataSource;

use Jte\Cache;

class CacheTemplateDataSource implements TemplateDataSource
{
    /** @var  Cache */
    protected $cache;
    /** @var ParserTemplateDataSource */
    protected $parser;

    public function __construct($cache, $parser)
    {
        $this->cache = $cache;
        $this->parser = $parser;
    }

    public function getTemplateData($templateName)
    {
        if ($this->cache->isCacheExists($templateName)) {
            $data = include $this->cache->getFilePath($templateName);
        } else {
            $data = $this->parser->getTemplateData($templateName);
            $this->cache->save($templateName, '<?php return ' . var_export($data, 1) . ';');
        }

        return $data;
    }
}
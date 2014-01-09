<?php

namespace Jte;

use Jte\LogicProcessor\EvalLogicProcessor;
use Jte\LogicProcessor\RequireLogicProcessor;
use Jte\Other\Cache;
use Jte\Other\TemplateFactory;
use Jte\Parser\BlockSignatureParser;
use Jte\Parser\MarkupParser;
use Jte\Parser\SectionParser;
use Jte\Source\FileSource;
use Jte\TemplateDataSource\CacheTemplateDataSource;
use Jte\TemplateDataSource\ParserTemplateDataSource;

class Jte
{
    protected $templateDir;
    protected $isSaladCooked = false;
    protected $renderer = null;
    protected $cacheParams;
    protected $parserParams;

    protected $cacheDefaultParams = [
        'fileMode' => 0777,
        'dirMode' => 0777,
        'dir' => '/cache',
        'useCache' => false
    ];
    protected $parserDefaultParams = [];

    public function __construct($templateDir, $cacheParams = [], $parserParams = [])
    {
        $this->templateDir = $templateDir;
        $this->cacheParams = $cacheParams + $this->cacheDefaultParams;
        $this->parserParams = $parserParams + $this->parserDefaultParams;
        $this->parserParams['templateDir'] = $templateDir;
    }

    public function render($templateName, $blockName, $params = [])
    {
        if (!$this->isSaladCooked) {
            $this->makeSalad();
        }

        return $this->renderer->render($templateName, $blockName, $params);
    }

    protected function makeSalad()
    {
        $parser = new ParserTemplateDataSource(
            new FileSource($this->parserParams['templateDir']),
            new SectionParser(),
            new MarkupParser(new BlockSignatureParser())
        );

        if ($this->cacheParams['useCache']) {
            $cache = new Cache(
                $this->cacheParams['dir'],
                $this->cacheParams['fileMode'],
                $this->cacheParams['dirMode']
            );
            $templateDataSource = new CacheTemplateDataSource($cache, $parser);
            $logicProcessor = new RequireLogicProcessor($cache);
        } else {
            $templateDataSource = $parser;
            $logicProcessor = new EvalLogicProcessor();
        }

        $templateFactory = new TemplateFactory($templateDataSource);
        $this->renderer = new Renderer($templateFactory, $logicProcessor);

        $this->isSaladCooked = true;
    }
} 
<?php
namespace Jte\TemplateDataSource;

use Jte\Parser\MarkupParser;
use Jte\Parser\SectionParser;
use Jte\Source\FileSource;

class ParserTemplateDataSource implements TemplateDataSource
{
    /** @var  FileSource */
    protected $source;
    /** @var  SectionParser */
    protected $templateParser;
    /** @var  MarkupParser */
    protected $markupParser;

    public function __construct($source, $templateParser, $markupParser)
    {
        $this->source = $source;
        $this->templateParser = $templateParser;
        $this->markupParser = $markupParser;
    }

    public function getTemplateData($templateName)
    {
        $text = $this->source->get($templateName);
        $data = $this->templateParser->parse($text);

        foreach ($data['blocks'] as $blockName => &$block) {
            if (!isset($block['markup'])) {
                throw new \Exception('No markup for block ' . $blockName);
            }

            $block['markup'] = $this->markupParser->parse($block['markup']);
        }

        return $data;
    }
}
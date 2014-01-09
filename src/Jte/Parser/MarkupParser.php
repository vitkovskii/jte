<?php
namespace Jte\Parser;

class MarkupParser extends BaseParser
{
    protected $result;
    /**
     * @var BlockSignatureParser
     */
    private $blockSignatureParser;

    function __construct($blockSignatureParser)
    {
        $this->blockSignatureParser = $blockSignatureParser;
    }

    public function parse($markup)
    {
        $this->setText($markup);
        $this->result = [];

        while (!$this->isEnd()) {
            $text = $this->getUntilBlock();

            if ($text != '') {
                $this->appendNode(MarkupNodeEnum::NODE_TEXT, ['value' => $text]);
            }

            if (!$this->isEnd()) {
                $block = $this->getBlock();
                $this->appendNode(MarkupNodeEnum::NODE_BLOCK, $block);
            }
        }

        return $this->result;
    }

    protected function getUntilBlock()
    {
        $result = '';
        while (!$this->isEnd() && !$this->testCurrentString('[[')) {
            $result .= $this->getCurrentChar();
            $this->nextChar();
        }

        return ($result);
    }

    protected function getBlock()
    {
        if (!$this->testCurrentString('[[')) {
            throw new \Exception('Block start expected!');
        }

        $this->nextChar();
        $this->nextChar();

        $blockSignature = $this->getBlockSignature();
        $block = $this->blockSignatureParser->parse($blockSignature);

        $position = $this->getNextNotWhiteSpacePosition($this->position);
        if ($this->testString($position, '{{')){
            $this->skipWhiteSpace();
            $content = $this->getBlockContent();

            // todo: block can be parsed in one pass
            $clone = new self($this->blockSignatureParser);
            $result = $clone->parse($content);
            $block['markup'] = $result;
        }

        return $block;
    }

    protected function getBlockSignature()
    {
        $data = '';
        while (!$this->isEnd() && !$this->testCurrentString(']]')) {
            $data .= $this->getCurrentChar();
            $this->nextChar();
        }

        if ($this->isEnd()) {
            throw new \Exception('Can\'t read block data! ' . $data);
        }

        $this->nextChar();
        $this->nextChar();

        return trim($data);

    }

    protected function appendNode($type, $data)
    {
        $data['type'] = $type;
        $this->result[] = $data;
    }

    protected function getBlockContent()
    {
        if (!$this->testCurrentString('{{')) {
            throw new \Exception('Block content start expected!');
        }

        $this->nextChar();
        $this->nextChar();

        $data = '';
        $stack = [1];
        while (!$this->isEnd() && (count($stack) > 0)) {
            if ($this->testCurrentString('{{')) {
                array_push($stack, 1);
            }
            if ($this->testCurrentString('}}')) {
                array_pop($stack);
            }
            if (count($stack) != 0) {
                $data .= $this->getCurrentChar();
            }
            $this->nextChar();
        }

        if ($this->isEnd() && !count($stack)) {
            throw new \Exception('Can\'t read block content! ' . $data);
        }

        $this->nextChar();

        return trim($data);
    }
}
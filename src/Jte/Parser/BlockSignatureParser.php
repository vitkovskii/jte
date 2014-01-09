<?php
namespace Jte\Parser;

class BlockSignatureParser extends BaseParser
{
    public function parse($text)
    {
        $this->setText($text);

        $this->skipWhiteSpace();

        $blockName = $this->getBlockName();
        if ($blockName == '') {
            $blockName = null;
        }

        if ($this->testNextNotWhiteSpaceChar('=')) {
            $this->skipWhiteSpace();
            $this->nextChar();
            $paramName = $this->getAssignedParamName();
        } else {
            $paramName = null;
        }

        return ['name' => $blockName, 'param' => $paramName];
    }

    protected function getBlockName()
    {
        $data = '';
        while (!$this->isEnd() && $this->getCurrentChar() != '=') {
            $data .= $this->getCurrentChar();

            $this->nextChar();
        }

        return trim($data);
    }

    protected function getAssignedParamName()
    {
        $data = '';
        while (!$this->isEnd()) {
            $data .= $this->getCurrentChar();

            $this->nextChar();
        }

        return trim($data);
    }
}
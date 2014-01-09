<?php
namespace Jte\Parser;

class SectionParser extends BaseParser
{
    public function parse($text)
    {
        $this->setText($text);
        $result = ['blocks' => []];
        $this->skipWhiteSpace();
        while (!$this->isEnd()) {
            $section = $this->getSectionName();

            $this->skipWhiteSpace();

            switch ($section) {
                case 'logic':
                case 'markup':
                    $blockName = $this->getBlockName();
                    $contents = $this->getSectionContents();
                    $result['blocks'][$blockName][$section] = $contents;
                    break;
                case 'boot':
                    $contents = $this->getSectionContents();
                    $result['boot'] = $contents;
                    break;
                default:
                    throw new \Exception('Unknown section ' . $section);
            }
            $this->skipWhiteSpace();
        }

        return $result;
    }

    protected function getSectionName()
    {
        $section = '';
        while (!$this->isEnd() && $this->getCurrentChar() != ' ') {
            $section .= $this->getCurrentChar();
            $this->nextChar();
        }

        if ($this->isEnd()) {
            throw new \Exception('Can\'t read section "' . $section . '"');
        }

        return trim($section);
    }

    protected function getBlockName()
    {
        $block = '';
        while (!$this->isEnd() && $this->getCurrentChar() != '{') {
            $block .= $this->getCurrentChar();
            $this->nextChar();
        }

        if ($this->isEnd()) {
            throw new \Exception('Can\'t read section! ' . $block);
        }

        return trim($block);
    }

    protected function getSectionContents()
    {
        if ($previousChar = $this->getCurrentChar() != '{') {
            throw new \Exception('Section content expected, but found ' . $this->getCurrentChar());
        }

        $result = '';
        $this->nextChar();
        while (!$this->isSectionEndCombination($previousChar, $this->getCurrentChar()) && !$this->isEnd()) {
            $result .= $this->getCurrentChar();
            $previousChar = $this->getCurrentChar();
            $this->nextChar();
        }

        if ($this->isEnd() && !$this->isSectionEndCombination($previousChar, $this->getCurrentChar())) {
            throw new \Exception('Endless section');
        }

        $this->nextChar();

        return trim($result, "\r\n");
    }

    protected function isSectionEndCombination($previousChar, $currentChar)
    {
        return ($previousChar == "\n" || $previousChar == "\r") && $currentChar == '}';
    }
}
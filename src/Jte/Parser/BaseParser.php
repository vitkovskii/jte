<?php
namespace Jte\Parser;

class BaseParser
{
    protected $encoding = 'utf-8';

    protected $position;
    protected $text;
    protected $textLength;

    protected function setText($text)
    {
        $this->position = 0;
        $this->text = $text;
        $this->textLength = mb_strlen($text, $this->encoding);
    }

    protected function isWhiteSpace($char)
    {
        return $char == ' ' || $char == "\n" || $char == "\r";
    }

    protected function skipWhiteSpace()
    {
        $this->position = $this->getNextNotWhiteSpacePosition($this->position);
    }

    protected function getNextNotWhiteSpacePosition($position)
    {
        while (!$this->isLastPosition($position) && ($this->isWhiteSpace($this->getChar($position)))) {
            $position = $this->getNextCharPosition($position);
        }

        return $position;
    }

    protected function readCurlyBlock()
    {
        if ($this->getCurrentChar() != '{') {
            throw new \Exception('Curly block expected, but found ' . $this->getCurrentChar());
        }

        $curly = '';
        $stack = [1];
        $this->nextChar();
        while (count($stack) != 0 && !$this->isEnd()) {
            if ($this->getCurrentChar() == '{') {
                array_push($stack, 1);
            }
            if ($this->getCurrentChar() == '}') {
                array_pop($stack);
            }

            if (count($stack) != 0) {
                $curly .= $this->getCurrentChar();
            }

            $this->nextChar();
        }

        if ($this->isEnd() && count($stack)) {
            throw new \Exception('Can\'t read curly block! ' . $curly);
        }

        return trim($curly);
    }

    protected function isEnd()
    {
        return $this->isLastPosition($this->position);
    }

    protected function isLastPosition($position)
    {
        return $position == $this->textLength;
    }

    protected function getCurrentChar()
    {
        return $this->getChar($this->position);
    }

    protected function getChar($position)
    {
        return $this->getCharFromString($position, $this->text);
    }

    protected function getCharFromString($position, $string)
    {
        return mb_substr($string, $position, 1, $this->encoding);
    }

    protected function testNextNotWhiteSpaceChar($char)
    {
        $position = $this->position;
        while (!$this->isLastPosition($position) && $this->isWhiteSpace($this->getChar($position))) {
            $position = $this->getNextCharPosition($position);
        }

        if ($this->isLastPosition($position)) {
            return false;
        }

        return $this->getChar($position) == $char;
    }

    protected function nextChar()
    {
        if ($this->isEnd()) {
            throw new \Exception("Unexpected end of file!");
        }
        $this->position = $this->getNextCharPosition($this->position);
    }

    protected function getNextCharPosition($position)
    {
        return $position + 1;
    }

    protected function testCurrentString($string)
    {
        return $this->testString($this->position, $string);
    }

    protected function testString($position, $string)
    {
        $i = 0;
        while ($i < strlen($string)) {
            if ($this->getChar($position) != $string[$i]) {
                return false;
            }

            $position = $this->getNextCharPosition($position);
            $i++;
        }

        return true;
    }
} 
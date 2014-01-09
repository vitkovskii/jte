<?php
namespace Jte\Parser;

class BaseParserTest extends \PHPUnit_Framework_TestCase
{
    function testPositioning()
    {
        $parser = new BaseParserMock();
        $parser->setText('текст');

        $parser->nextChar();
        $parser->nextChar();

        $this->assertEquals('к', $parser->getCurrentChar());
    }
}

class BaseParserMock extends BaseParser
{
    public function setText($text)
    {
        parent::setText($text);
    }

    public function nextChar()
    {
        parent::nextChar();
    }

    public function getCurrentChar()
    {
        return parent::getCurrentChar();
    }
}
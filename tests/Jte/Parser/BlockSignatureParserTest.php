<?php
namespace Jte\Parser;

class BlockSignatureParserTest extends \PHPUnit_Framework_TestCase
{
    public function testParse()
    {
        $parser = new BlockSignatureParser();

        $result = $parser->parse('    block');
        $this->assertEquals('block', $result['name']);
        $this->assertEquals(null, $result['param']);

        $result = $parser->parse('    block=param');
        $this->assertEquals('block', $result['name']);
        $this->assertEquals('param', $result['param']);

        $result = $parser->parse('    block      =param');
        $this->assertEquals('block', $result['name']);
        $this->assertEquals('param', $result['param']);

        $result = $parser->parse('    block=     param');
        $this->assertEquals('block', $result['name']);
        $this->assertEquals('param', $result['param']);

        $result = $parser->parse('    =     param');
        $this->assertEquals(null, $result['name']);
        $this->assertEquals('param', $result['param']);
    }
}
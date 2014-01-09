<?php
namespace Jte\Parser;

class MarkupParserTest extends \PHPUnit_Framework_TestCase
{
    public function testParse()
    {
        $parser = new MarkupParser(new BlockSignatureParser());

        $parser->parse('[[block]] {{ }}');

        $data = $parser->parse('<text>abba</text>');
        $this->assertEquals($data[0]['type'], MarkupNodeEnum::NODE_TEXT);
        $this->assertEquals($data[0]['value'], '<text>abba</text>');

        $data = $parser->parse('<text>[[ abba ]]</text>[[abba]]');
        $this->assertEquals(MarkupNodeEnum::NODE_TEXT, $data[0]['type']);
        $this->assertEquals('<text>', $data[0]['value']);
        $this->assertEquals(MarkupNodeEnum::NODE_BLOCK, $data[1]['type']);
        $this->assertEquals('abba', $data[1]['name']);
        $this->assertEquals(MarkupNodeEnum::NODE_TEXT, $data[2]['type']);
        $this->assertEquals('</text>', $data[2]['value']);
        $this->assertEquals(MarkupNodeEnum::NODE_BLOCK, $data[3]['type']);
        $this->assertEquals('abba', $data[3]['name']);

        $data = $parser->parse('
        <text>
            [[ abba ]] {{
                content
                [[ inside some block! ]]
                {{
                    content2
                }}
            }}
        </text>
        [[ abba ]] {{ }}'
        );
        $this->assertEquals(MarkupNodeEnum::NODE_TEXT, $data[0]['type']);
        $this->assertEquals('<text>', trim($data[0]['value']));
        $this->assertEquals(MarkupNodeEnum::NODE_BLOCK, $data[1]['type']);

        $this->assertEquals('abba', $data[1]['name']);
        $content = $data[1]['markup'];
        $this->assertEquals(MarkupNodeEnum::NODE_TEXT, $content[0]['type']);
        $this->assertEquals('content', trim($content[0]['value']));
        $this->assertEquals(MarkupNodeEnum::NODE_BLOCK, $content[1]['type']);
        $this->assertEquals('inside some block!', $content[1]['name']);

        $content = $content[1]['markup'];
        $this->assertEquals(MarkupNodeEnum::NODE_TEXT, $content[0]['type']);
        $this->assertContains('content2', $content[0]['value']);
    }
}
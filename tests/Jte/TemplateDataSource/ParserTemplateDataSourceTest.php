<?php
namespace Jte\Parser;

use Jte\TemplateDataSource\ParserTemplateDataSource;

class ParserTemplateDataSourceTest extends \PHPUnit_Framework_TestCase
{
    public function testParse()
    {
        $parser = new ParserTemplateDataSource(new SourceMock('template1.jte'), new SectionParser(), new MarkupParser(new BlockSignatureParser()));
        $data = $parser->getTemplateData('');

        $this->assertArrayHasKey('blocks', $data);
        $this->assertArrayHasKey('head', $data['blocks']);
        $this->assertArrayHasKey('body', $data['blocks']);
        $this->assertArrayHasKey('body_bottom', $data['blocks']);
        $this->assertContains('some php logic;', $data['blocks']['head']['logic']);
        $this->assertContains('<some><markup></markup></some>', $data['blocks']['head']['markup'][0]['value']);
        $this->assertContains('body logic', $data['blocks']['body']['logic']);
        $this->assertContains('body markup', $data['blocks']['body']['markup'][0]['value']);

        $check = false;
        foreach($data['blocks']['body_bottom']['markup'] as $block) {
            if ($block['type'] == MarkupNodeEnum::NODE_BLOCK && $block['name'] == 'placeholder') {
                $this->assertContains('some text', $block['markup'][0]['value']);
                $this->assertContains('another_placeholder', $block['markup'][1]['name']);
                $check = true;
            }
        }

        if (!$check) {
            $this->assertTrue(false);
        }

        $this->assertArrayHasKey('boot', $data);
        $this->assertContains('$a = true;', $data['boot']);
    }
}

class SourceMock
{
    protected $fileName;

    function __construct($fileName)
    {
        $this->fileName = $fileName;
    }

    public function get()
    {
        return file_get_contents(__DIR__ . '/../Templates/' . $this->fileName);
    }
}
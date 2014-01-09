<?php
namespace Jte\Parser;

class SectionParserTest extends \PHPUnit_Framework_TestCase
{
    protected function getText($fileName)
    {
        return file_get_contents(__DIR__ . '/../Templates/' . $fileName);
    }

    public function testParse()
    {
        $parser = new SectionParser();

        $data = $parser->parse($this->getText('template1.jte'));

        $this->assertArrayHasKey('blocks', $data);
        $this->assertArrayHasKey('head', $data['blocks']);
        $this->assertArrayHasKey('body', $data['blocks']);
        $this->assertArrayHasKey('logic', $data['blocks']['head']);
        $this->assertArrayHasKey('markup', $data['blocks']['head']);

        $this->assertContains('some php logic;', $data['blocks']['head']['logic']);
        $this->assertContains('<some><markup></markup></some>', $data['blocks']['head']['markup']);

        $this->assertContains('body logic', $data['blocks']['body']['logic']);
        $this->assertContains('body markup', $data['blocks']['body']['markup']);
    }
}
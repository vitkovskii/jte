<?php
namespace Jte;

use Jte\LogicProcessor\EvalLogicProcessor;
use Jte\Other\BlockDesigner;
use Jte\Parser\BlockSignatureParser;
use Jte\Parser\MarkupParser;
use Jte\Parser\SectionParser;
use Jte\Source\FileSource;
use Jte\TemplateDataSource\ParserTemplateDataSource;

class RendererTest extends \PHPUnit_Framework_TestCase
{
    public function testBasicRender()
    {
        $blockDesigner = new BlockDesigner();

        $logic = "replace('body')->with(param('body content')); replace('footer')->with(block('footer content', ['content' => 'footer_content']));";
        $logic2 = "replace('footer_place_holder')->with(param('content'));";
        $data = [
            'blocks' => [
                'main' =>
                    $blockDesigner->produceBlockNode('main', null, $logic, [
                        $blockDesigner->produceTextNode('<html>'),
                        $blockDesigner->produceBlockNode('placeholder', null, null, [
                            $blockDesigner->produceTextNode('<head></head>'),
                            $blockDesigner->produceTextNode('<body>'),
                            $blockDesigner->produceBlockNode('body', null, null, [
                                $blockDesigner->produceTextNode('body text'),
                            ]),
                            $blockDesigner->produceBlockNode('footer', null, null, [
                                $blockDesigner->produceTextNode('no footer'),
                            ]),
                            $blockDesigner->produceTextNode('<body>'),
                        ]),
                        $blockDesigner->produceTextNode('</html>'),
                    ]),
                'footer content' =>
                    $blockDesigner->produceBlockNode('footer content', null, $logic2, [
                        $blockDesigner->produceBlockNode('footer_place_holder', null, null, [])
                    ])
            ]
        ];

        $templateFactory = $this->getMock('\StdClass', ['getTemplate']);

        $template = new Template('', $data);
        $templateFactory->expects($this->at(0))->method('getTemplate')->will($this->returnValue($template));
        $renderer = new Renderer($templateFactory, new EvalLogicProcessor());

        $text = $renderer->render('', 'main', ['body content' => 'another body content']);

        $this->assertContains('another body content', $text);
        $this->assertContains('<head></head>', $text);
        $this->assertContains('footer_content', $text);
    }

    public function testExtends()
    {
        $blockDesigner = new BlockDesigner();

        $baseData = [
            'blocks' => [
                'main' =>
                    $blockDesigner->produceBlockNode('main', null, null, [
                        $blockDesigner->produceBlockNode('base', null, null, [
                            $blockDesigner->produceTextNode('base_value'),
                        ])
                    ]),
            ]
        ];

        $childData = [
            'boot' => "extend('base');",
            'blocks' => [
                'main' =>
                    $blockDesigner->produceBlockNode('main', null, "replace('child')->with(block('block1', ['param1' => 'param1value']));", [
                        $blockDesigner->produceBlockNode('base', null, null, [
                            $blockDesigner->produceTextNode('child_value1'),
                            $blockDesigner->produceBlockNode('parent'),
                        ]),
                        $blockDesigner->produceBlockNode('child', null, null, [
                            $blockDesigner->produceTextNode('undef'),
                        ])
                    ]),
                'block1' =>
                    $blockDesigner->produceBlockNode('block1', null, null, [
                        $blockDesigner->produceTextNode('defined1'),
                        $blockDesigner->produceBlockNode(null, 'param1'),
                    ]),
            ]
        ];

        $childChildData = [
            'boot' => "extend('child');",
            'blocks' => [
                'main' =>
                    $blockDesigner->produceBlockNode('main', null, "replace('child')->with('defined2');", [
                        $blockDesigner->produceBlockNode('base', null, null, [
                            $blockDesigner->produceBlockNode('parent'),
                            $blockDesigner->produceTextNode('child_value2'),
                            $blockDesigner->produceBlockNode('context_save', 'context_save', null, [
                                $blockDesigner->produceTextNode('undef3'),
                            ])
                        ]),
                        $blockDesigner->produceBlockNode('child', null, null, [
                            $blockDesigner->produceTextNode('undef2'),
                        ])
                    ]),

            ]
        ];


        $templateFactory = $this->getMock('\StdClass', ['getTemplate']);

        $childChildTemplate = new Template('', $childChildData);
        $childTemplate = new Template('', $childData);
        $baseTemplate = new Template('', $baseData);

        $templateFactory->expects($this->at(0))->method('getTemplate')->will($this->returnValue($childChildTemplate));
        $templateFactory->expects($this->at(1))->method('getTemplate')->will($this->returnValue($childTemplate));
        $templateFactory->expects($this->at(2))->method('getTemplate')->will($this->returnValue($baseTemplate));

        $renderer = new Renderer($templateFactory, new EvalLogicProcessor());

        $text = $renderer->render('', 'main', ['context_save' => 'defined3']);

        $this->assertContains('child_value1', $text);
        $this->assertContains('child_value2', $text);
        $this->assertContains('defined1', $text);
        $this->assertContains('defined2', $text);
        $this->assertContains('defined3', $text);
        $this->assertContains('base_value', $text);
        $this->assertContains('param1value', $text);
        $this->assertGreaterThan(strpos($text, 'child_value1'), strpos($text, 'base_value'));
        $this->assertGreaterThan(strpos($text, 'child_value1'), strpos($text, 'child_value2'));
    }

    public function testWhitespace()
    {
        $templateParser = new ParserTemplateDataSource(new FileSource(__DIR__ . '/Templates'), new SectionParser(), new MarkupParser(new BlockSignatureParser()));
        $template = new Template('', $templateParser->getTemplateData('whitespace1.jte'));

        $templateFactory = $this->getMock('\StdClass', ['getTemplate']);
        $templateFactory->expects($this->any())->method('getTemplate')->will($this->returnValue($template));
        $renderer = new Renderer($templateFactory, new EvalLogicProcessor());


        $result = $renderer->render($template, 'test1');
        $this->assertEquals('    hello!', $result);

        $result = $renderer->render($template, 'test2');
        $this->assertEquals("    hello1!\n    hello2!", $result);

        $result = $renderer->render($template, 'test3');
        $this->assertEquals("    hello1!\n    hello2!", $result);

        $result = $renderer->render($template, 'test4');
        $this->assertEquals("        hello1!\n    hello2!\n    hello2!", $result);

        $result = $renderer->render($template, 'test5');
        $this->assertEquals("        hello1!\n    hello2!\n    hello2!", $result);

        $result = $renderer->render($template, 'test6');
        $this->assertEquals("    hello1!\n    hello2!", $result);

        $result = $renderer->render($template, 'test7');
        $this->assertEquals("    hello1!\nhello1!\nhello1!\n    hello2!", $result);

        $result = $renderer->render($template, 'test8');
        $this->assertEquals("    hello1! hello1! hello1!\n    hello2!", $result);
    }
}
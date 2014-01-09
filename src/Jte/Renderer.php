<?php
namespace Jte;

use Jte\Language\BlockLogic\Data;
use Jte\LogicProcessor\LogicProcessor;
use Jte\Parser\MarkupNodeEnum;

class Renderer
{
    protected $params;
    protected $result;
    protected $blockProcessors;

    /** @var  TemplateFactory */
    protected $templateFactory;

    /** @var  LogicProcessor */
    private $logicProcessor;

    public function __construct($templateFactory, $logicProcessor)
    {
        $this->templateFactory = $templateFactory;
        $this->logicProcessor = $logicProcessor;
    }

    /**
     * @param $templateName
     * @param $blockName
     * @param array $params
     * @return string
     */
    public function render($templateName, $blockName, $params = [])
    {
        $this->params = $params;
        $this->result = '';

        /** @var Template $template */
        $template = $this->templateFactory->getTemplate($templateName);

        if ($template->isBootExists()) {
            $this->bootTemplate($template, $params);
        }

        while ($template->hasParent()) {
            $template = $template->getParent();
        }

        $baseBlock = [
            'type' => MarkupNodeEnum::NODE_BLOCK,
            'name' => $blockName,
            'template' => $template,
            'logic' => new Data()
        ];
        $baseMarkup = [$baseBlock];

        $this->markupIterate($baseMarkup, $baseBlock, $baseBlock);

        return $this->result;
    }

    /**
     * @param $markup
     * @param null $logicBlock
     * @param $selfBlock
     * @return string
     */
    protected function markupIterate($markup, $logicBlock, $selfBlock)
    {
        foreach ($markup as &$node) {
            $savedContext = $logicBlock;
            switch ($node['type']) {

                case MarkupNodeEnum::NODE_TEXT:
                    $this->result .= $node['value'];
                    break;

                case MarkupNodeEnum::NODE_BLOCK:
                    $blockName = $node['name'];

                    if ($this->isParentBlock($blockName)) {
                        $context = $this->getParentBlockContext($logicBlock);
                    } elseif ($childTemplate = $this->isBlockHasChild($blockName, $logicBlock)) {
                        $context = $this->getChildBlockContext($childTemplate, $blockName, $node);
                    } elseif ($this->isBlockHasReplacements($blockName, $logicBlock)) {
                        $context = $this->getReplacementContext($blockName, $logicBlock, $node);
                    } elseif ($this->isBlockHasParamAttachment($node, $logicBlock, $selfBlock)) {
                        $context = $this->getParamAttachmentContext($node, $logicBlock, $selfBlock);
                    } else {
                        $context = [$node, null];
                    }

                    $node = $context[0];
                    if (isset($context[1])) {
                        $logicBlock = $context[1];
                    }

                    if (isset($node['markup'])) {
                        $this->markupIterate($node['markup'], $logicBlock, $node);
                    }

                    break;

                case MarkupNodeEnum::NODE_BLOCK_REFERENCE:
                    $node = $this->makeChildNode($logicBlock['template'], $node['ref'], $node, $node['params']);
                    $this->markupIterate($node['markup'], $node, $node);

                    break;

                case MarkupNodeEnum::NODE_SELF_BLOCK:
                    $selfBlock['params'] = $node['params'];
                    $this->markupIterate($selfBlock['originalMarkup'], $logicBlock, $selfBlock);
            }
            $logicBlock = $savedContext;
        }
    }

    protected function isParentBlock($blockName)
    {
        return $blockName == 'parent';
    }

    protected function getParentBlockContext($logicBlock)
    {
        $inheritedBlockName = $logicBlock['name'];

        /** @var Template $parentTemplate */
        $parentTemplate = $logicBlock['template']->getParent();
        if ($parentTemplate->isBlockExists($inheritedBlockName)) {
            $node = $this->makeChildNode($parentTemplate, $inheritedBlockName, $logicBlock['parentBlock']);

            return [$node, $node];
        }

        return [$logicBlock['parentBlock'], $logicBlock];
    }

    protected function isBlockHasChild($blockName, $logicBlock)
    {
        return $childTemplate = $logicBlock['template']->getChildBlockTemplate($blockName);
    }

    protected function getChildBlockContext($childTemplate, $blockName, $currentNode)
    {
        $node = $this->makeChildNode($childTemplate, $blockName, $currentNode);

        return [$node, $node];
    }

    protected function isBlockHasReplacements($blockName, $logicBlock)
    {
        return isset($logicBlock['logic']->getReplaces()[$blockName]);
    }

    protected function getReplacementContext($blockName, $logicBlock, $currentNode)
    {
        if (isset($currentNode['markup'])) {
            $node['originalMarkup'] = $currentNode['markup'];
        }
        $node['markup'] = $logicBlock['logic']->getReplaces()[$blockName];

        return [$node, null];
    }

    protected function isBlockHasParamAttachment($node, $logicBlock, $selfBlock)
    {
        if (!isset($node['param'])) {
            return false;
        }

        $paramName = $node['param'];

        return
            isset($this->params[$paramName]) ||
            isset($logicBlock['params'][$paramName]) ||
            $selfBlock['params'][$paramName];
    }

    protected function getParamAttachmentContext($node, $logicBlock, $selfBlock)
    {
        $paramName = $node['param'];

        if (isset($this->params[$node['param']])) {
            $value = $this->params[$paramName];
        } elseif (isset($logicBlock['params'][$node['param']])) {
            $value = $logicBlock['params'][$paramName];
        } else {
            $value = $selfBlock['params'][$paramName];
        }

        $node['markup'] = [['type' => MarkupNodeEnum::NODE_TEXT, 'value' => $value]];

        return [$node, null];
    }

    /**
     * @param Template $template
     * @param $blockName
     * @param $parentBlock
     * @param null $params
     * @return mixed
     */
    protected function makeChildNode($template, $blockName, $parentBlock, $params = null)
    {
        if ($template->isBlockLogicExists($blockName)) {
            if ($params == null) {
                $params = $this->params;
            }
            $logic = $this->logicProcessor->processBlockLogic($template, $blockName, $params);
        } else {
            $logic = new Data();
        }

        $node['markup'] = $template->getBlockMarkup($blockName);
        $node['type'] = MarkupNodeEnum::NODE_BLOCK;
        $node['template'] = $template;
        $node['logic'] = $logic;
        $node['parentBlock'] = $parentBlock;
        $node['name'] = $blockName;
        $node['params'] = $params;

        return $node;
    }

    /**
     * @param Template $template
     * @param $params
     * @throws \Exception
     */
    public function bootTemplate($template, $params)
    {
        if (!$template->isBootExists()) {
            throw new \Exception('No boot for template');
        }

        $bootData = $this->logicProcessor->processBootLogic($template, $params);
        if ($bootData->getExtend() != null) {
            /** @var Template $parent */
            $parent = $this->templateFactory->getTemplate($bootData->getExtend());
            $template->setParent($parent);
            $parent->setChild($template);

            if ($parent->isBootExists()) {
                $this->bootTemplate($parent, $params);
            }
        }
    }
}
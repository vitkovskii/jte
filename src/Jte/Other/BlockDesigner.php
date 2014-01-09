<?php
namespace Jte\Other;

use Jte\Parser\MarkupNodeEnum;

class BlockDesigner
{
    public function produceNode($blockType, $params)
    {
        $params['type'] = $blockType;

        return $params;
    }

    public function produceBlockNode($name, $param = null, $logic = null, $linkedBlocks = null)
    {
        $params['name'] = $name;
        $params['param'] = $param;
        if ($logic != null) {
            $params['logic'] = $logic;
        }

        if ($linkedBlocks != null) {
            $params['markup'] = $linkedBlocks;
        }

        return $this->produceNode(MarkupNodeEnum::NODE_BLOCK, $params);
    }

    public function produceTextNode($text)
    {
        $params['value'] = $text;

        return $this->produceNode(MarkupNodeEnum::NODE_TEXT, $params);
    }

    public function linkNode(&$parentNode, $childNode)
    {
        $parentNode['markup'][] = $childNode;
    }
} 
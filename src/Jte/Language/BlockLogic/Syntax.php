<?php
namespace Jte\Language\BlockLogic;

use Jte\Language\BlockLogic\Replace;
use Jte\Parser\MarkupNodeEnum;
use Jte\Parser\MarkupParser;

function param($name, $default = null)
{
    if (!isset(\Jte\Language\inputGateWay()[$name])) {
        if ($default === null) {
            throw new \Exception('Param ' . $name . ' not passed');
        } else {
            return $default;
        }
    }

    return \Jte\Language\inputGateWay()[$name];
}

function replace($blockData)
{
    return new Replace(\Jte\Language\outputGateWay(), $blockData);
}

function value($value)
{
    return ['type' => MarkupNodeEnum::NODE_TEXT, 'value' => htmlspecialchars($value)];
}

function block($blockName, $params = null)
{
    return ['type' => MarkupNodeEnum::NODE_BLOCK_REFERENCE, 'ref' => $blockName, 'params' => $params];
}

function self($params = null)
{
    return ['type' => MarkupNodeEnum::NODE_SELF_BLOCK, 'params' => $params];
}

function iterator($param, $function, $sep = "\n")
{
    $i = 0;
    $replace = [];
    foreach ($param as $value) {
        $result = $function($value, $i);
        if ($result != null) {
            if (isset($result['type']) || !is_array($result)) {
                $replace[] = $result;
                if ($sep != '') {
                    $replace[] = ['type' => MarkupNodeEnum::NODE_TEXT, 'value' => $sep];
                }
            } else {
                foreach ($result as $item) {
                    $replace[] = $item;
                    if ($sep != '') {
                        $replace[] = ['type' => MarkupNodeEnum::NODE_TEXT, 'value' => $sep];
                    }
                }
            }
        }
        $i++;
    }

    return array_slice($replace, 0, -1);
}
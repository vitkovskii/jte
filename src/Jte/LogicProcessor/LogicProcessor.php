<?php
namespace Jte\LogicProcessor;

use Jte\Template;

abstract class LogicProcessor
{
    protected function getLogicText($body, $namespace)
    {
        return
            '   namespace ' . $namespace . ';
                \Jte\Language\inputGateWay($params);
                \Jte\Language\outputGateWay(new Data());' .
            $body .
            '   return \Jte\Language\outputGateWay();';
    }

    /**
     * @param Template $template
     * @param $params
     * @return mixed
     */
    abstract function processBootLogic($template, $params);

    /**
     * @param Template $template
     * @param $blockName
     * @param $params
     * @return mixed
     */
    abstract function processBlockLogic($template, $blockName, $params);
} 
<?php
namespace Jte\LogicProcessor;

use Jte\Template;

class EvalLogicProcessor extends LogicProcessor
{
    /**
     * @param Template $template
     * @param $params
     * @return mixed
     */
    public function processBootLogic($template, $params)
    {
        return eval($this->getLogicText($template->getBoot(), 'Jte\Language\BootLogic'));
    }

    /**
     * @param Template $template
     * @param $blockName
     * @param $params
     * @return mixed
     */
    public function processBlockLogic($template, $blockName, $params)
    {
        return eval($this->getLogicText($template->getBlockLogic($blockName), 'Jte\Language\BlockLogic'));
    }
}
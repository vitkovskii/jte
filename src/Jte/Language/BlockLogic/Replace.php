<?php
namespace Jte\Language\BlockLogic;

use Jte\Parser\MarkupNodeEnum;
use Jte\Parser\MarkupParser;

class Replace
{
    private $blockName;
    /** @var  Data */
    private $data;

    public function __construct($data, $blockName)
    {
        $this->data = $data;
        $this->blockName = $blockName;
    }

    public function with($value)
    {
        if (isset($value['type'])) {
            $value = [$value];
        } elseif (is_string($value)) {
            $value = [['type' => MarkupNodeEnum::NODE_TEXT, 'value' => htmlspecialchars($value)]];
        }

        $this->data->addReplace($this->blockName, $value);
    }
}


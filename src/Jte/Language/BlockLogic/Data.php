<?php
namespace Jte\Language\BlockLogic;

class Data
{
    protected $replaces;

    public function __construct()
    {
    }

    public function addReplace($blockName, $value)
    {
        $this->replaces[$blockName] = $value;
    }

    public function getReplaces()
    {
        return $this->replaces;
    }
}


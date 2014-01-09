<?php
namespace Jte\Language\BootLogic;

class Data
{
    protected $extend = null;

    public function __construct()
    {
    }

    /**
     * @return mixed
     */
    public function getExtend()
    {
        return $this->extend;
    }

    /**
     * @param mixed $extends
     */
    public function setExtend($extends)
    {
        $this->extend = $extends;
    }
}


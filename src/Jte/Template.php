<?php
namespace Jte;

class Template
{
    protected $data = [];
    protected $parent = null;
    /** @var Template */
    protected $child = null;
    protected $name;

    public function __construct($name, $templateData)
    {
        $this->name = $name;
        $this->data = $templateData;
    }

    /**
     * @param Template $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * @param \Jte\Template $child
     */
    public function setChild($child)
    {
        $this->child = $child;
    }

    /**
     * @return Template
     */
    public function getChild()
    {
        return $this->child;
    }

    public function getBlockLogic($blockName)
    {
        if (!$this->isBlockLogicExists($blockName)) {
            throw new \Exception('Block logic "' . $blockName . '" not found');
        }

        return $this->data['blocks'][$blockName]['logic'];
    }

    public function getBlockMarkup($blockName)
    {
        if (!$this->isBlockExists($blockName)) {
            throw new \Exception('Block "' . $blockName . '" not found');
        }

        return $this->data['blocks'][$blockName]['markup'];
    }

    public function isBlockExists($blockName)
    {
        return isset($this->data['blocks'][$blockName]);
    }

    public function isBlockLogicExists($blockName)
    {
        return isset($this->data['blocks'][$blockName]['logic']);
    }

    public function getBoot()
    {
        if (!$this->isBootExists()) {
            throw new \Exception('No boot information found!');
        }

        return $this->data['boot'];
    }

    public function isBootExists()
    {
        return isset($this->data['boot']);
    }

    /**
     * @return null
     */
    public function getParent()
    {
        return $this->parent;
    }

    public function hasParent()
    {
        return $this->parent != null;
    }

    public function getChildBlockTemplate($blockName)
    {
        $childData = false;
        if ($this->isChildExists()) {
            $childData = $this->getChild()->getChildBlockTemplate($blockName);
        }

        if ($childData) {
            return $childData;
        }

        if ($this->isBlockExists($blockName)) {
            return $this;
        } else {
            return false;
        }
    }

    public function getBlock($blockName)
    {
        if (!isset($this->data['blocks'][$blockName])) {
            throw new \Exception('Block "' . $blockName . '" not found');
        }

        return $this->data['blocks'][$blockName];
    }

    public function isChildExists()
    {
        return $this->child != null;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
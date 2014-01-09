<?php
namespace Jte\Other;

use Jte\Template;
use Jte\TemplateDataSource\TemplateDataSource;

class TemplateFactory
{
    /** @var TemplateDataSource */
    private $templateDataSource;

    public function __construct($templateDataSource)
    {
        $this->templateDataSource = $templateDataSource;
    }

    public function getTemplate($templateName)
    {
        return new Template($templateName, $this->templateDataSource->getTemplateData($templateName));
    }
}
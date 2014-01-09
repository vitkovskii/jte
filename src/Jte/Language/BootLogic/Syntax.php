<?php
namespace Jte\Language\BootLogic;

function extend($templateName)
{
    \Jte\Language\outputGateWay()->setExtend($templateName);
}
<?php
namespace Jte\Language;

function outputGateWay($set = null)
{
    static $data = null;

    if ($set) {
        return $data = $set;
    } else {
        return $data;
    }
}

function inputGateWay($set = null)
{
    static $params = [];

    if ($set) {
        return $params = $set;
    } else {
        return $params;
    }
}
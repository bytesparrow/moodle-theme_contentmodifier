<?php

defined('MOODLE_INTERNAL') || die;

$plugin->version   = 2022092900;
$plugin->requires  = 2016051900;
$plugin->component = 'theme_contentmodifier';
$THEME->hidefromselector = true;
$plugin->dependencies = ['theme_boost' => 2020060900];
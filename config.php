<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Theme Contentmodifier - core_renderer class file
 *
 * @package    theme_contentmodifier
 * @copyright  2022 Bernhard Strehl <moodle@software.bernhard-strehl.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */



require_once(__DIR__ . '/lib.php');

$THEME->name = 'contentmodifier';


######  See section "Duplicate the settings from Boost"
###### @ https://docs.moodle.org/dev/Creating_a_theme_based_on_boost 

$THEME->doctype = 'html5';
$THEME->parents = ['boost'];
$THEME->sheets = ['custom'];
$THEME->supportscssoptimisation = false;
$THEME->yuicssmodules = [];
$THEME->enable_dock = false;
$THEME->editor_sheets = [];
$THEME->hidefromselector = true;

$THEME->rendererfactory = 'theme_extended_overridden_renderer_factory';
$THEME->requiredblocks = ''; #wichtig, sonst geht h5p nicht
$THEME->usefallback = true;
$THEME->haseditswitch = true;
// By default, all boost theme do not need their titles displayed.
$THEME->activityheaderconfig = [
    'notitle' => true
];

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
 * Theme Contentmodifier - core_modifier_base class file
 * If you want to modify Moodle's main content, implement a class
 * class core_modifier extends \core_modifier_base
 *
 * @package    theme_contentmodifier
 * @copyright  2022 Bernhard Strehl <moodle@software.bernhard-strehl.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
//namespace "\"


defined('MOODLE_INTERNAL') || die;


/**
 * If you want to implement a content-modifier in your theme/plugin then
 * implement a class extending this class.
 * @package    theme_contentmodifier
 * @copyright  2022 Bernhard Strehl <moodle@software.bernhard-strehl.de>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @category output
 */
abstract class core_modifier_base
{

    /**
     * Used to MODIFY the main-content. 
     * $main_content is passed as reference, so any changes to this
     * variable have immediate effect.
     * Implement this function in your own plugin if you need it.
     * @param string $main_content the rendered html-main-content
     */
    public function modify_main_content(&$main_content)
    {
        //can be overridden in any module
    }

    /**
     * Used to append content to main-content. 
     * $main_content is passed as reference, so no return is necessary
     * Do NOT override this function, it is used by get_content_to_attach_to_main()
     * @param string $main_content the rendered html-main-content
     */
    public function append_content_to_main(&$main_content)
    {
        $lastdivposition = strrpos($main_content, "</div>");
        if (!$lastdivposition) {
            throw new Exception("Main content was crippled and has an unexpected format");
        }
        $cutmain = substr($main_content, 0, $lastdivposition);
        $main_content = $cutmain . $this->get_content_to_attach_to_main() . "</div>";
    }

    /**
     * implement this function in your plugin. 
     * HTML that is returned by this function will be added
     * to moodle's main-content (via function append_content_to_main())
     * @return String $html
     */
    abstract function get_content_to_attach_to_main();
}

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
 * Theme Contentmodifier - outputmodifiers_factory class file and helper-classes
 *
 * @package    theme_contentmodifier
 * @copyright  2022 Bernhard Strehl <moodle@software.bernhard-strehl.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
//namespace "\"


defined('MOODLE_INTERNAL') || die;

/**
 * This class extends renderer_factory_base and is used to get possible classnames
 * for content-modifiers extending the class class core_modifier
 *
 * @package    theme_contentmodifier
 * @copyright  2022 Bernhard Strehl <moodle@software.bernhard-strehl.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class outputmodifiers_factory_base extends renderer_factory_base
{

    /**
     * taken from function standard_renderer_classnames.
     * Returns possible class names extending the class
     *  "class core_modifier extends \core_modifier_base"
     * Plugins/Themes implementing this can then modify / extend moodle's main_content
     *
     * @param string $component name; atm only 'core' is implemented
     * @param string $subtype optional subtype such as 'news' resulting to:
     *              '\core\output\news_renderer'
     *              or '\core\output\news\renderer' ; currently NOT implemented
     * @return array[] Each element of the array is an array with keys:
     *                 classname - The class name to search
     *                 autoloaded - Does this classname assume autoloading?
     *                 validwithprefix - Is this class name valid when a prefix is added to it?
     *                 validwithoutprefix - Is this class name valid when no prefix is added to it?
     * @throws coding_exception
     */
    protected function standard_modifier_classnames($component, $subtype = null)
    {
        $classnamedefs = $this->standard_renderer_classnames($component, $subtype);
        foreach ($classnamedefs as &$class_def) {
            $class_def["classname"] = str_replace("renderer", "modifier", $class_def["classname"]);
            $class_def["classname"] = "\\" . $class_def["classname"];
            $class_def["classname"] = str_replace("\\\\", "\\", $class_def["classname"]);
        }
        return $classnamedefs;
    }

    /**
     * function must be implemented but is not used.
     */
    public function get_renderer(moodle_page $page, $component, $subtype = null, $target = null)
    {
        throw new coding_exception("that was not xtpected.");
    }
};


/**
 * This is outputmodifiers factory.
 * It allows modules and themes to modify the rendered html-main-content
 *
 * It will load any code from theme/mytheme/core_modifier.php and
 * theme/parenttheme/core_modifier.php, if then exist
 *

 * @copyright  2022 Bernhard Strehl <moodle@software.bernhard-strehl.de>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 4.0
 * @package theme_contentmodifier
 * @category output
 */
class overridden_outputmodifiers_factory extends outputmodifiers_factory_base
{

     /**
     * empty Constructor.
     *
     * */
    public function __construct()
    {
    }


    /**
     * Gets a list of all implemented core-output modifiers.
     *
     * @return array[] an array of objects of type core_modifier_base
     * @throws coding_exception
     */
    public function get_modifiers()
    {
        $all_modifier_objects = array();

        $classnamedefs = $this->standard_modifier_classnames("core");
        #var_dump($classnamedefs);exit;
        //plugins AND themes.
        $pm = \core_plugin_manager::instance();
        $plugintypes = $pm->get_plugin_types();


        $namespaces = array();
        foreach ($plugintypes as $plugtype => $typepath) {
            $plugins = $pm->get_installed_plugins($plugtype);

            foreach ($plugins as $pluginname => $version) {
                $namespaces[] = $plugtype . "_" . $pluginname;
            }
        }

        foreach ($namespaces as $namespace) {
            foreach ($classnamedefs as $classnamedef) {
                $class = $namespace . $classnamedef['classname'];
                if (class_exists($class)) {
                    $cm = new $class();
                    $all_modifier_objects[] = $cm;
                }
                if(!$classnamedef['autoloaded'])
                {
                    //hier könnte man nun die datei laden oder so.. aber nur falls sie existiert
                    //not implemented
                }
            }
        }
        return $all_modifier_objects;
    }


}

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
     */
    abstract function get_content_to_attach_to_main();
}

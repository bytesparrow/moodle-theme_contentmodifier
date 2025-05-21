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
 * This is custom renderer_factory.
 * It allows modules and themes to modify the rendered html-main-content
 * and adds the possibility to modules to override the core_renderers
 * (at the moment for core_customfield, maybe extended)
 *
 * It will load any code from /mytheme/classes/output/core_modifier.php and
 * /mytheme/classes/output/core_customfield_renderer.php
 *

 * @copyright  2022 Bernhard Strehl <moodle@software.bernhard-strehl.de>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 4.0
 * @package theme_contentmodifier
 * @category output
 */
class theme_extended_overridden_renderer_factory extends theme_overridden_renderer_factory {

  /**
   * @var array An array of renderer prefixes
   * taken from core
   */
  protected $prefixes = array();

  /**
   * Constructor.
   * @param theme_config $theme the theme we are rendering for.
   */
  public function __construct(theme_config $theme) {
    parent::__construct($theme);
    // YES, YES it works1!!
  }

  /**
   * this function extends the parent::get_renderer-method so that also
   * modules can provide a renderer (and not only themes)
   *
   * @param moodle_page $page the page the renderer is outputting content for.
   * @param string $component name such as 'core', 'mod_forum' or 'qtype_multichoice'.
   * @param string $subtype optional subtype such as 'news' resulting to 'mod_forum_news'
   * @param string $target one of rendering target constants
   * @return renderer_base an object implementing the requested renderer interface.
   */
  public function get_renderer(moodle_page $page, $component, $subtype = null, $target = null) {
    // Per default lädt er einen renderer im theme oder den plugin-eigenen render.
    $foundrenderer = parent::get_renderer($page, $component, $subtype, $target);

    if ($component == 'core_customfield') {
      $rendererclassname = get_class($foundrenderer);
      // Das ist ein core-renderer. Gibts keinen besseren?
      if (strpos($rendererclassname, "core_") === 0) {
        $classnamedefs = $this->standard_renderer_classnames($component, $subtype);
        $alternativerenderers = $this->get_renderer_objects($classnamedefs, $page, $target);
        $foundrenderer = $alternativerenderers[0] ?: $foundrenderer;
      }
    }
    return $foundrenderer;
  }

  /**
   * Gets a list of all implemented core-output modifiers.
   *
   * @param moodle_page $PAGE the global $PAGE-object
   * @return array[core_modifier_base] an array of objects of type core_modifier_base
   * @throws coding_exception
   */
  public function get_modifiers(moodle_page $page) {
    $classnamedefs = $this->standard_modifier_classnames("core");
    return $this->get_renderer_objects($classnamedefs, $page, null);
  }

  /**
   * retrieves objects of type \core_modifier_base or \renderer
   * for the $classdefinitions retrieved via this->standard_renderer_classnames
   *
   * @param array $classdefinitions retrieved via this->standard_renderer_classnames or this->standard_modifier_classnames
   * @param moodle_page $page
   * @param $target one of the Renderer-Targets  (constants RENDERER_TARGET_*)
   */
  private function get_renderer_objects(array $classdefinitions, moodle_page $page, $target = null) {
    $allrenderers = array();
    // Plugins AND themes.
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
      foreach ($classdefinitions as $classnamedef) {
        $class = $namespace . $classnamedef['classname'];
        if (class_exists($class)) {
          $cm = new $class($page, $target);
          $allrenderers[] = $cm;
        }
        if (!$classnamedef['autoloaded']) {
          // Hier könnte man nun die datei laden oder so.. aber nur falls sie existiert.
          //trigger-error entfernt, da dies "100x" kommt. quasi für jedes plugin. media_youtube, format_topics,dataformat_html,profilefield_menu
          //trigger_error("Handling non-autoloaded classes is not supported in " . __FILE__ . " for " . $class, E_USER_NOTICE);
        }
      }
    }
    return $allrenderers;
  }

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
  protected function standard_modifier_classnames($component, $subtype = null) {
    $classnamedefs = $this->standard_renderer_classnames($component, $subtype);
    foreach ($classnamedefs as &$classdef) {
      $classdef["classname"] = str_replace("renderer", "modifier", $classdef["classname"]);
      $classdef["classname"] = "\\" . $classdef["classname"];
      $classdef["classname"] = str_replace("\\\\", "\\", $classdef["classname"]);
    }
    return $classnamedefs;
  }

}

Theme Contentmodifier
==========

This theme is provided not as a standalone-theme but as a parenttheme to other themes. It allows modifying  moodle's main content with any html. If you want to use it you should create a sub-theme extending this theme AND for example the boost-theme

## Getting started

1. Clone the repository to your theme folder
2. write your own theme extending THIS theme AND any other (for example boost)

    2.1 make sure to place an empty class 
        ``` class core_renderer extends \theme_contentmodifier\output\core_renderer``` in folder **classes/output**
    2.2. In theme's config.php place the line: 
        ``` $THEME->rendererfactory = 'theme_extended_overridden_renderer_factory'; ```

3. Enable YOUR theme in your Moodle installation under the appearance settings
4. You can now use the features of this theme by implementing the class
    ``` theme_YOURS\output\core_modifier extends \core_modifier_base  ```

## Usage

Feel free to fork this project or simply use it as a template for doing your
own custom modifications to moodle.

## Requirements

This theme requires Moodle 4.0+

## License ##

2022 Bernhard Strehl <moodle@bytesparrow.de>

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program.  If not, see <https://www.gnu.org/licenses/>.

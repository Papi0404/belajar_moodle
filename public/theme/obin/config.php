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
 * Theme OBIN - configuration.
 *
 * Boost child theme: $THEME->layouts is NOT redefined wholesale. Moodle
 * automatically inherits a child theme's parent layouts for any key not
 * explicitly overridden in config.php (see theme_config::__construct() in
 * lib/outputlib.php — the merge is key-by-key). This is the standard
 * behaviour for simple Boost child themes that only change the colour
 * palette and typography without altering page structure.
 *
 * The only exception is the 'frontpage' layout (home page, logged-out
 * visitors), which is overridden to add a photo banner and tagline (see
 * layout/frontpage.php and templates/frontpage.mustache). All other pages
 * continue to use Boost's layouts unchanged.
 *
 * @package    theme_obin
 * @copyright  2026 Citoyenneté et Inclusion Numérique (OBIN) <contact@obin-asso.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$THEME->name = 'obin';
$THEME->parents = ['boost'];
$THEME->sheets = [];
$THEME->editor_sheets = [];
$THEME->enable_dock = false;
$THEME->yuicssmodules = [];
$THEME->rarrow = '&#x25BA;';
$THEME->larrow = '&#x25C4;';
$THEME->requiredblocks = '';
$THEME->undeletableblocktypes = '';
$THEME->haseditswitch = true;
$THEME->usescourseindex = true;
$THEME->rendererfactory = 'theme_overridden_renderer_factory';

$THEME->layouts = [
    'frontpage' => [
        'file' => 'frontpage.php',
        'regions' => ['side-pre'],
        'defaultregion' => 'side-pre',
        'options' => ['nonavbar' => true],
    ],
];

$THEME->scss = function($theme) {
    return theme_obin_get_main_scss_content($theme);
};
$THEME->prescsscallback = 'theme_obin_get_pre_scss';
$THEME->extrascsscallback = 'theme_obin_get_extra_scss';

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
 * Theme OBIN - Hook callback for before_standard_html_head.
 *
 * Injects Google Fonts preconnect and stylesheet links into the <head>.
 *
 * Using @import url(...) inside SCSS is not viable here: scssphp (the library
 * used by Moodle) does not reliably treat a remote @import added via
 * append_raw_scss() as a plain CSS passthrough — it tries to resolve it as a
 * local SCSS file, which silently aborts the whole compilation
 * (get_css_content_from_scss() catches the exception and falls back to Boost's
 * default CSS without any visible error). The result: no theme customisation
 * (colours, gradient, fonts) is applied at all. Hence the injection via HTML.
 *
 * @package    theme_obin
 * @copyright  2026 Citoyenneté et Inclusion Numérique (OBIN) <contact@obin-asso.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_obin\hook\output;

/**
 * Hook callback: injects Google Fonts into the HTML <head>.
 */
class before_standard_html_head {

    /**
     * Callback invoked by Moodle's Hook API.
     *
     * @param \core\hook\output\before_standard_html_head $hook
     */
    public static function callback(\core\hook\output\before_standard_html_head $hook): void {
        $hook->add_html(
            '<link rel="preconnect" href="https://fonts.googleapis.com">'
            . '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>'
            . '<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&family=Lato:wght@400;700&display=swap">'
        );
    }
}

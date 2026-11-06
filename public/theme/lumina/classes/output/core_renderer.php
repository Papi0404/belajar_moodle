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

namespace theme_lumina\output;

defined('MOODLE_INTERNAL') || die();

/**
 * Lumina LMS Core Renderer.
 *
 * Extends Boost's core renderer to provide custom modern UI components
 * aligned with the Lumina LMS design system.
 *
 * @package    theme_lumina
 * @copyright  2026 Antigravity
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_renderer extends \theme_boost\output\core_renderer {

    /**
     * Renders user avatar with Lumina LMS styling.
     *
     * @param \user_picture $userpicture
     * @return string HTML
     */
    public function render_user_picture(\user_picture $userpicture) {
        $userpicture->size = 40;
        return parent::render_user_picture($userpicture);
    }
}


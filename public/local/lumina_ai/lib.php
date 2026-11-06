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
 * lib.php — Optional Moodle callbacks for Lumina AI.
 *
 * @package    local_lumina_ai
 * @copyright  2026 Lumina LMS
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Called after plugin installation to set sensible defaults.
 */
function local_lumina_ai_after_install(): void {
    set_config('enabled', 1, 'local_lumina_ai');
    set_config('chatbot_title', 'Lumina AI Academic Tutor', 'local_lumina_ai');
    set_config('chatbot_color', '#4338CA', 'local_lumina_ai');
    set_config('preferred_provider', 'auto', 'local_lumina_ai');
    set_config('max_requests_per_hour', 50, 'local_lumina_ai');
}


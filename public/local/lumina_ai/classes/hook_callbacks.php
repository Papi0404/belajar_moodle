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
 * Hook callbacks for Lumina AI plugin.
 *
 * @package    local_lumina_ai
 * @copyright  2026 Lumina LMS
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_lumina_ai;

use core\hook\output\before_footer_html_generation;

/**
 * Hook callbacks class.
 */
class hook_callbacks {

    /**
     * Inject the Lumina AI chatbot HTML before page footer.
     * This runs on EVERY page, EVERY theme, as long as this plugin is installed.
     *
     * @param before_footer_html_generation $hook
     */
    public static function before_footer_html_generation(before_footer_html_generation $hook): void {
        global $PAGE, $OUTPUT, $USER, $CFG;

        // Guard: only for logged-in, non-guest users.
        if (!isloggedin() || isguestuser()) {
            return;
        }

        // Guard: skip admin install/upgrade pages.
        $pagetype = $PAGE->pagetype ?? '';
        $skippagetypes = ['admin-index', 'admin-cliupgrade', 'admin-upgradesettings', 'login-index'];
        if (in_array($pagetype, $skippagetypes)) {
            return;
        }

        // Guard: skip if plugin is disabled in settings.
        if (!get_config('local_lumina_ai', 'enabled')) {
            return;
        }

        // Build template context.
        $context = [
            'wwwroot'       => $CFG->wwwroot,
            'sesskey'       => sesskey(),
            'userid'        => (int)$USER->id,
            'userfirstname' => s($USER->firstname ?? ''),
            'chatbot_title' => get_config('local_lumina_ai', 'chatbot_title') ?: 'Lumina AI Academic Tutor',
            'chatbot_color' => get_config('local_lumina_ai', 'chatbot_color') ?: '#4338CA',
            'api_endpoint'  => $CFG->wwwroot . '/local/lumina_ai/ai_chat.php',
        ];

        $html = $OUTPUT->render_from_template('local_lumina_ai/chatbot', $context);
        $hook->add_html($html);
    }
}


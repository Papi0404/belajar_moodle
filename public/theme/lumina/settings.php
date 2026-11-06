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
 * Lumina LMS Theme settings.
 *
 * @package    theme_lumina
 * @copyright  2026 Antigravity
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    $settings->hidden = false;

    // Semester label setting.
    $name = 'theme_lumina/semester_label';
    $title = get_string('semester_label_setting', 'theme_lumina');
    $description = get_string('semester_label_setting_desc', 'theme_lumina');
    $default = 'Semester Ganjil 2026/2027';
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
    $settings->add($setting);

    // AI Chat API Key setting (Gemini).
    $name = 'theme_lumina/gemini_api_key';
    $title = get_string('gemini_api_key_setting', 'theme_lumina');
    $description = get_string('gemini_api_key_setting_desc', 'theme_lumina');
    $default = '';
    $setting = new admin_setting_configpasswordunmask($name, $title, $description, $default);
    $settings->add($setting);

    // Brand color setting.
    $name = 'theme_lumina/brandcolor';
    $title = get_string('brandcolor_setting', 'theme_lumina');
    $description = get_string('brandcolor_setting_desc', 'theme_lumina');
    $default = '#4338CA';
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
    $settings->add($setting);
}

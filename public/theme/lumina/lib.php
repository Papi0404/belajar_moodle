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
 * Lumina LMS Theme lib file.
 *
 * @package    theme_lumina
 * @copyright  2026 Antigravity
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Returns the main SCSS content for Lumina theme.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_lumina_get_main_scss_content($theme) {
    global $CFG;

    $scss = '';
    // Load parent Boost preset first.
    $boostpreset = $CFG->dirroot . '/theme/boost/scss/preset/default.scss';
    if (file_exists($boostpreset)) {
        $scss .= file_get_contents($boostpreset);
    }

    // Load Lumina custom SCSS.
    $luminamain = $CFG->dirroot . '/theme/lumina/scss/lumina.scss';
    if (file_exists($luminamain)) {
        $scss .= "\n" . file_get_contents($luminamain);
    }

    return $scss;
}

/**
 * Get SCSS to prepend.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_lumina_get_pre_scss($theme) {
    $scss = '';

    // Lumina Design System Core Variables.
    $scss .= "\$primary: #4338ca !default;\n";
    $scss .= "\$indigo: #4338ca !default;\n";
    $scss .= "\$secondary: #06b6d4 !default;\n";
    $scss .= "\$cyan: #06b6d4 !default;\n";
    $scss .= "\$success: #10b981 !default;\n";
    $scss .= "\$green: #10b981 !default;\n";
    $scss .= "\$info: #06b6d4 !default;\n";
    $scss .= "\$warning: #f59e0b !default;\n";
    $scss .= "\$yellow: #f59e0b !default;\n";
    $scss .= "\$danger: #ef4444 !default;\n";
    $scss .= "\$red: #ef4444 !default;\n";

    // Typography variables.
    $scss .= "\$font-family-base: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif !default;\n";
    $scss .= "\$headings-font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif !default;\n";
    $scss .= "\$headings-font-weight: 700 !default;\n";

    // Surface & Border variables.
    $scss .= "\$body-bg: #f8f9ff !default;\n";
    $scss .= "\$body-color: #0b1c30 !default;\n";
    $scss .= "\$card-bg: #ffffff !default;\n";
    $scss .= "\$card-border-color: #e2e8f0 !default;\n";
    $scss .= "\$card-border-radius: 1rem !default;\n";
    $scss .= "\$btn-border-radius: 0.5rem !default;\n";
    $scss .= "\$input-border-radius: 0.5rem !default;\n";
    $scss .= "\$input-border-color: #cbd5e1 !default;\n";
    $scss .= "\$input-focus-border-color: #4338ca !default;\n";
    $scss .= "\$input-focus-box-shadow: 0 0 0 3px rgba(67, 56, 202, 0.2) !default;\n";

    // Inherit boost pre-scss functionality if needed.
    if (function_exists('theme_boost_get_pre_scss')) {
        $scss .= theme_boost_get_pre_scss($theme);
    }

    return $scss;
}

/**
 * Inject additional SCSS into compilation.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_lumina_get_extra_scss($theme) {
    $content = '';
    if (function_exists('theme_boost_get_extra_scss')) {
        $content .= theme_boost_get_extra_scss($theme);
    }
    return $content;
}

/**
 * Get compiled css.
 *
 * @return string compiled css
 */
function theme_lumina_get_precompiled_css() {
    global $CFG;
    $file = $CFG->dirroot . '/theme/lumina/style/moodle.css';
    if (file_exists($file)) {
        return file_get_contents($file);
    }
    return '';
}

/**
 * Returns available user preferences for Lumina.
 *
 * @return array[]
 */
function theme_lumina_user_preferences(): array {
    return [
        'drawer-open-block' => [
            'type' => PARAM_BOOL,
            'null' => NULL_NOT_ALLOWED,
            'default' => false,
            'permissioncallback' => [core_user::class, 'is_current_user'],
        ],
        'drawer-open-index' => [
            'type' => PARAM_BOOL,
            'null' => NULL_NOT_ALLOWED,
            'default' => true,
            'permissioncallback' => [core_user::class, 'is_current_user'],
        ],
        'lumina-rail-collapsed' => [
            'type' => PARAM_BOOL,
            'null' => NULL_NOT_ALLOWED,
            'default' => false,
            'permissioncallback' => [core_user::class, 'is_current_user'],
        ],
    ];
}

/**
 * Serves files associated with theme settings.
 */
function theme_lumina_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    if (function_exists('theme_boost_pluginfile')) {
        return theme_boost_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, $options);
    }
    send_file_not_found();
}

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
 * Admin settings for Lumina AI plugin.
 *
 * @package    local_lumina_ai
 * @copyright  2026 Lumina LMS
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_lumina_ai', get_string('pluginname', 'local_lumina_ai'));
    $ADMIN->add('localplugins', $settings);

    // -------------------------------------------------------------------------
    // Global Toggle
    // -------------------------------------------------------------------------
    $settings->add(new admin_setting_heading(
        'local_lumina_ai/general_heading',
        get_string('settings_general', 'local_lumina_ai'),
        get_string('settings_general_desc', 'local_lumina_ai')
    ));

    $settings->add(new admin_setting_configcheckbox(
        'local_lumina_ai/enabled',
        get_string('settings_enabled', 'local_lumina_ai'),
        get_string('settings_enabled_desc', 'local_lumina_ai'),
        1
    ));

    // -------------------------------------------------------------------------
    // Chatbot Appearance
    // -------------------------------------------------------------------------
    $settings->add(new admin_setting_heading(
        'local_lumina_ai/appearance_heading',
        get_string('settings_appearance', 'local_lumina_ai'),
        ''
    ));

    $settings->add(new admin_setting_configtext(
        'local_lumina_ai/chatbot_title',
        get_string('settings_chatbot_title', 'local_lumina_ai'),
        get_string('settings_chatbot_title_desc', 'local_lumina_ai'),
        'Lumina AI Academic Tutor',
        PARAM_TEXT
    ));

    $settings->add(new admin_setting_configcolourpicker(
        'local_lumina_ai/chatbot_color',
        get_string('settings_chatbot_color', 'local_lumina_ai'),
        get_string('settings_chatbot_color_desc', 'local_lumina_ai'),
        '#4338CA'
    ));

    // -------------------------------------------------------------------------
    // AI Provider Configuration
    // -------------------------------------------------------------------------
    $settings->add(new admin_setting_heading(
        'local_lumina_ai/provider_heading',
        get_string('settings_provider', 'local_lumina_ai'),
        get_string('settings_provider_desc', 'local_lumina_ai')
    ));

    $providersoptions = [
        'auto'     => get_string('settings_provider_auto', 'local_lumina_ai'),
        'gemini'   => 'Google Gemini',
        'openai'   => 'OpenAI GPT',
        'deepseek' => 'DeepSeek',
        'ollama'   => 'Ollama (Local)',
        'fallback' => get_string('settings_provider_fallback', 'local_lumina_ai'),
    ];
    $settings->add(new admin_setting_configselect(
        'local_lumina_ai/preferred_provider',
        get_string('settings_preferred_provider', 'local_lumina_ai'),
        get_string('settings_preferred_provider_desc', 'local_lumina_ai'),
        'auto',
        $providersoptions
    ));

    $settings->add(new admin_setting_configpasswordunmask(
        'local_lumina_ai/gemini_api_key',
        get_string('settings_gemini_api_key', 'local_lumina_ai'),
        get_string('settings_gemini_api_key_desc', 'local_lumina_ai'),
        ''
    ));

    $settings->add(new admin_setting_configpasswordunmask(
        'local_lumina_ai/openai_api_key',
        get_string('settings_openai_api_key', 'local_lumina_ai'),
        get_string('settings_openai_api_key_desc', 'local_lumina_ai'),
        ''
    ));

    $settings->add(new admin_setting_configpasswordunmask(
        'local_lumina_ai/deepseek_api_key',
        get_string('settings_deepseek_api_key', 'local_lumina_ai'),
        get_string('settings_deepseek_api_key_desc', 'local_lumina_ai'),
        ''
    ));

    $settings->add(new admin_setting_configtext(
        'local_lumina_ai/ollama_endpoint',
        get_string('settings_ollama_endpoint', 'local_lumina_ai'),
        get_string('settings_ollama_endpoint_desc', 'local_lumina_ai'),
        'http://localhost:11434',
        PARAM_URL
    ));

    // -------------------------------------------------------------------------
    // Rate Limiting
    // -------------------------------------------------------------------------
    $settings->add(new admin_setting_heading(
        'local_lumina_ai/ratelimit_heading',
        get_string('settings_ratelimit', 'local_lumina_ai'),
        ''
    ));

    $settings->add(new admin_setting_configtext(
        'local_lumina_ai/max_requests_per_hour',
        get_string('settings_max_requests', 'local_lumina_ai'),
        get_string('settings_max_requests_desc', 'local_lumina_ai'),
        '50',
        PARAM_INT
    ));
}


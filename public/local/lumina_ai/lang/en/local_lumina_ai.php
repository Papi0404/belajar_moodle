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
 * Language strings for Lumina AI plugin.
 *
 * @package    local_lumina_ai
 * @copyright  2026 Lumina LMS
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Lumina AI Academic Tutor';
$string['plugindesc']  = 'Standalone AI Academic Tutor chatbot for Moodle. Works on any theme via Moodle hook system.';

// Settings.
$string['settings_general']              = 'General Settings';
$string['settings_general_desc']         = 'Configure the Lumina AI chatbot behavior.';
$string['settings_enabled']              = 'Enable Lumina AI';
$string['settings_enabled_desc']         = 'Show the AI chatbot assistant on all pages for logged-in users.';
$string['settings_appearance']           = 'Appearance';
$string['settings_chatbot_title']        = 'Chatbot Title';
$string['settings_chatbot_title_desc']   = 'Title displayed in the chatbot header.';
$string['settings_chatbot_color']        = 'Primary Color';
$string['settings_chatbot_color_desc']   = 'The brand/accent color for the chatbot FAB and header.';
$string['settings_provider']             = 'AI Provider';
$string['settings_provider_desc']        = 'Configure which AI provider powers the Lumina AI chatbot. API keys set here take priority over environment variables. The plugin also automatically reads active Moodle AI provider settings.';
$string['settings_preferred_provider']   = 'Preferred Provider';
$string['settings_preferred_provider_desc'] = 'Choose your preferred AI provider. "Auto" will detect the first available provider from Moodle AI settings and environment variables.';
$string['settings_provider_auto']        = 'Auto-detect (Recommended)';
$string['settings_provider_fallback']    = 'Built-in Fallback (No external API)';
$string['settings_gemini_api_key']       = 'Google Gemini API Key';
$string['settings_gemini_api_key_desc']  = 'Get your API key at https://aistudio.google.com/';
$string['settings_openai_api_key']       = 'OpenAI API Key';
$string['settings_openai_api_key_desc']  = 'Get your API key at https://platform.openai.com/';
$string['settings_deepseek_api_key']     = 'DeepSeek API Key';
$string['settings_deepseek_api_key_desc'] = 'Get your API key at https://platform.deepseek.com/';
$string['settings_ollama_endpoint']      = 'Ollama Endpoint URL';
$string['settings_ollama_endpoint_desc'] = 'URL to your local Ollama instance (e.g. http://localhost:11434).';
$string['settings_ratelimit']            = 'Rate Limiting';
$string['settings_max_requests']         = 'Max Requests Per Hour (per user)';
$string['settings_max_requests_desc']    = 'Maximum number of AI messages a user can send per hour. Set 0 for unlimited.';


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
 * Theme OBIN - version information.
 *
 * A Boost child theme for the "Citoyenneté et Inclusion Numérique" (OBIN)
 * association (French non-profit, loi 1901), committed to digital inclusion.
 * This theme adds no new features: it applies the association's official
 * graphic charter (colours, typography) on top of Boost, without modifying
 * its layouts.
 *
 * @package    theme_obin
 * @copyright  2026 Citoyenneté et Inclusion Numérique (OBIN) <contact@obin-asso.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->component = 'theme_obin';
$plugin->version   = 2026102403;
// Compatibility actually verified (dedicated test installs, theme activated,
// dashboard + admin + front page checked without error): Moodle 4.3, 4.5
// and 5.2 — no code changes were needed across these three versions.
// Intermediate versions (4.4, 5.0, 5.1) were not tested but are likely
// compatible; no breaking changes observed over the covered range.
// Notable for Moodle 5.x: the "public/" directory split (see Moodle's
// UPGRADING.md) only affects web-server configuration, not the theme code
// itself — theme_obin requires no modification for that.
$plugin->requires  = 2023100900; // Moodle 4.3 (oldest tested version).
$plugin->maturity  = MATURITY_BETA;
$plugin->release   = '1.3.12';
$plugin->dependencies = [
    'theme_boost' => 2023100900,
];

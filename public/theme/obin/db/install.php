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
 * Theme OBIN - script d'installation.
 *
 * @package    theme_obin
 * @copyright  2026 Citoyenneté et Inclusion Numérique (OBIN) <contact@obin-asso.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Ancienne étape d'installation, retirée (cf. classes/output/core_renderer.php).
 *
 * Cette fonction déposait auparavant le logo OBIN dans le réglage GLOBAL
 * "Administration du site > Apparence > Logos" (core_admin/logo et
 * core_admin/logocompact). Ce réglage s'applique à tout Moodle, quel que
 * soit le thème actif : un site qui désactivait ensuite theme_obin gardait
 * le logo OBIN sur son thème suivant.
 *
 * Le logo par défaut du thème est désormais servi uniquement via
 * core_renderer::get_logo_url()/get_compact_logo_url() (repli actif
 * seulement tant que theme_obin est le thème actif, et qui ne modifie
 * jamais aucun réglage global). Cette fonction ne fait donc plus rien ;
 * elle est conservée uniquement pour ne pas casser une mise à niveau depuis
 * une version où elle existait.
 */
function xmldb_theme_obin_install() {
    return true;
}

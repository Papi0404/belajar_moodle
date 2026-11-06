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
 * Theme OBIN - déclaration de confidentialité (RGPD).
 *
 * Un thème visuel ne stocke aucune donnée personnelle propre : on implémente
 * donc le "null_provider" standard de Moodle, ce qui est le cas d'usage
 * attendu et documenté pour ce type de plugin.
 *
 * @package    theme_obin
 * @copyright  2026 Citoyenneté et Inclusion Numérique (OBIN) <contact@obin-asso.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_obin\privacy;

defined('MOODLE_INTERNAL') || die();

/**
 * Privacy provider pour le thème OBIN : aucune donnée personnelle stockée.
 */
class provider implements \core_privacy\local\metadata\null_provider {
    /**
     * Retourne la raison (chaîne de langue) pour laquelle ce plugin n'a
     * pas besoin d'implémenter la collecte de données personnelles.
     *
     * @return string
     */
    public static function get_reason(): string {
        return 'privacy:metadata';
    }
}

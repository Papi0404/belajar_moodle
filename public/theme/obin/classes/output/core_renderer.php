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

namespace theme_obin\output;

/**
 * Renderer surchargé du thème OBIN.
 *
 * Seul ajout par rapport à theme_boost : obin_footer(), qui construit le
 * pied de page visible du site (cf. templates/footer_content.mustache),
 * utilisable depuis n'importe quel template via {{{ output.obin_footer }}}.
 * Ce mécanisme (classe core_renderer dans le namespace theme_<nom>\output)
 * est reconnu automatiquement par la "theme_overridden_renderer_factory"
 * déjà activée dans config.php - aucun câblage supplémentaire nécessaire.
 *
 * @package    theme_obin
 * @copyright  2026 Citoyenneté et Inclusion Numérique (OBIN) <contact@obin-asso.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_renderer extends \theme_boost\output\core_renderer {

    /**
     * Pied de page visible du site : nom du site, accroche, e-mail de
     * contact et liens, tous personnalisables depuis Administration du
     * site > Apparence > OBIN. Affiché sur toutes les pages (cf.
     * templates/theme_boost/footer.mustache, qui surcharge le footer de
     * Boost pour l'ensemble du site).
     *
     * @return string
     */
    public function obin_footer() {
        global $SITE;

        $tagline = get_config('theme_obin', 'footertagline');
        $email = get_config('theme_obin', 'footeremail');

        $links = theme_obin_get_footer_links();
        $sociallinks = theme_obin_get_social_links();

        $context = [
            'sitename' => format_string($SITE->fullname, true, ['context' => \context_system::instance()]),
            'tagline' => $tagline ? format_string($tagline) : '',
            'email' => $email ?: '',
            'links' => $links,
            'haslinks' => !empty($links),
            'year' => date('Y'),
            // Official Moodle logo ("powered by"), resolved via the renderer's
            // image_url rather than a hard-coded path: stays correct regardless
            // soit la structure de dossiers de la version de Moodle (cf.
            // of the "public/" restructure in 5.x, verified without impact here
            // precisely because we go through this mechanism).
            'moodlelogourl' => $this->image_url('moodlelogo', 'core')->out(false),
            'sociallinks' => $sociallinks,
            'hassociallinks' => !empty($sociallinks),
        ];

        return $this->render_from_template('theme_obin/footer_content', $context);
    }

    /**
     * Logo principal (utilisé notamment sur la page de connexion).
     *
     * Si l'administrateur du site a déposé son propre logo (Administration du
     * site > Apparence > Logos), on le respecte tel quel - comportement Boost
     * standard, inchangé. Sinon, on retombe sur le logo par défaut EMBARQUÉ
     * dans le thème (pix/logo.png) plutôt que de laisser l'emplacement vide.
     *
     * Ce logo par défaut n'est plus jamais écrit dans le réglage global
     * core_admin/logo (cf. ancien db/install.php, retiré : il modifiait un
     * réglage qui s'applique à TOUT Moodle, y compris si un autre thème est
     * activé ensuite - un site qui désactivait theme_obin gardait alors le
     * logo OBIN partout). Le fallback ici ne touche à rien : il n'est utilisé
     * que le temps que ce thème est actif, et disparaît dès qu'un logo est
     * réellement configuré ou qu'un autre thème est choisi.
     *
     * @param int|null $maxwidth
     * @param int $maxheight
     * @return \moodle_url|false
     */
    public function get_logo_url($maxwidth = null, $maxheight = 200) {
        $logourl = parent::get_logo_url($maxwidth, $maxheight);
        if (!empty($logourl)) {
            return $logourl;
        }

        return $this->image_url('logo', 'theme_obin');
    }

    /**
     * Logo compact (navbar). Même logique de repli que get_logo_url()
     * ci-dessus.
     *
     * @param int $maxwidth
     * @param int $maxheight
     * @return \moodle_url|false
     */
    public function get_compact_logo_url($maxwidth = 300, $maxheight = 300) {
        $logourl = parent::get_compact_logo_url($maxwidth, $maxheight);
        if (!empty($logourl)) {
            return $logourl;
        }

        return $this->image_url('logo', 'theme_obin');
    }
}

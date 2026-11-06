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
 * Theme OBIN - réglages d'administration.
 *
 * @package    theme_obin
 * @copyright  2026 Citoyenneté et Inclusion Numérique (OBIN) <contact@obin-asso.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    $settings = new admin_settingpage('themesettingobin', get_string('configtitle', 'theme_obin'));

    // Primary colour (OBIN blue by default, official brand guidelines).
    $name = 'theme_obin/brandcolor';
    $title = get_string('brandcolor', 'theme_obin');
    $description = get_string('brandcolor_desc', 'theme_obin');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#3D8FE8');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Secondary colour (teal by default, official brand guidelines).
    $name = 'theme_obin/secondarycolor';
    $title = get_string('secondarycolor', 'theme_obin');
    $description = get_string('secondarycolor_desc', 'theme_obin');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#64D6A8');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Whether the navbar logo is forced into a plain white silhouette (see
    // lib.php, ".navbar-brand .logo/img"). Enabled by default: this keeps the
    // exact behaviour existing installs already have (no visual change on
    // upgrade). Organisations uploading a logo that doesn't read well as a
    // flat white shape (multiple colours, fine detail, a wordmark...) can
    // turn it off here instead of having to pre-edit their logo file.
    $name = 'theme_obin/logowhitefilter';
    $title = get_string('logowhitefilter', 'theme_obin');
    $description = get_string('logowhitefilter_desc', 'theme_obin');
    $setting = new admin_setting_configcheckbox($name, $title, $description, '1');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Icônes des onglets d'administration : aucune (comportement natif de
    // Boost), emoji (apparence historique de ce thème) ou pictogrammes SVG
    // dessinés pour ce thème (voir theme_obin_get_menu_icons_scss() dans
    // lib.php). Un vrai choix à trois options plutôt qu'une case à cocher :
    // les trois rendus sont assez différents pour mériter chacun leur propre
    // libellé.
    //
    // Défaut "svg" (choisi après test visuel sur un site de démo) : ne
    // concerne que les NOUVELLES installations du thème - une installation
    // existante qui a déjà ce réglage enregistré (même sur une valeur créée
    // automatiquement lors d'une mise à jour précédente, ex. "emoji") garde
    // sa valeur actuelle telle quelle ; il faut la changer manuellement dans
    // Administration du site > Apparence > OBIN si on veut basculer.
    $name = 'theme_obin/menuicons';
    $title = get_string('menuicons', 'theme_obin');
    $description = get_string('menuicons_desc', 'theme_obin');
    $choices = [
        'none' => get_string('menuicons_none', 'theme_obin'),
        'emoji' => get_string('menuicons_emoji', 'theme_obin'),
        'svg' => get_string('menuicons_svg', 'theme_obin'),
    ];
    $setting = new admin_setting_configselect($name, $title, $description, 'svg', $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Raw advanced SCSS (before compilation) – for needs not covered
    // by the settings above; follows the same conventions as the Boost theme.
    $name = 'theme_obin/scsspre';
    $title = get_string('scsspre', 'theme_obin');
    $description = get_string('scsspre_desc', 'theme_obin');
    $setting = new admin_setting_configtextarea($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Raw advanced SCSS (after compilation).
    $name = 'theme_obin/scss';
    $title = get_string('scss', 'theme_obin');
    $description = get_string('scss_desc', 'theme_obin');
    $setting = new admin_setting_configtextarea($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Front-page banner image(s), uploaded by the site administrator.
    // Used by layout/frontpage.php; falls back to the default image
    // bundled with the theme (pix/hero.jpg). Several images can be
    // uploaded here: with two or more, the banner becomes an
    // auto-advancing slideshow (see theme_obin_get_hero_images() in
    // lib.php) instead of a single fixed photo.
    $name = 'theme_obin/heroimage';
    $title = get_string('heroimage', 'theme_obin');
    $description = get_string('heroimage_desc', 'theme_obin');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'heroimage', 0, [
        'maxfiles' => -1,
        'accepted_types' => ['.jpg', '.jpeg', '.png', '.webp'],
    ]);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Title and tagline displayed on the banner (defaults: official site name and
    // slogan de la charte graphique).
    $name = 'theme_obin/heroheading';
    $title = get_string('heroheading', 'theme_obin');
    $description = get_string('heroheading_desc', 'theme_obin');
    $setting = new admin_setting_configtext($name, $title, $description, 'Citoyenneté et Inclusion Numérique', PARAM_TEXT);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    $name = 'theme_obin/herosubheading';
    $title = get_string('herosubheading', 'theme_obin');
    $description = get_string('herosubheading_desc', 'theme_obin');
    $setting = new admin_setting_configtext(
        $name,
        $title,
        $description,
        'Chaque citoyen mérite d\'être acteur de sa vie numérique.',
        PARAM_TEXT
    );
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);


    // Couleur de fond du pied de page.
    $name = 'theme_obin/footerbgcolor';
    $title = get_string('footerbgcolor', 'theme_obin');
    $description = get_string('footerbgcolor_desc', 'theme_obin');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#1a3a5c');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Footer content displayed across the whole site (see
    // templates/theme_boost/footer.mustache et classes/output/core_renderer.php).
    $name = 'theme_obin/footertagline';
    $title = get_string('footertagline', 'theme_obin');
    $description = get_string('footertagline_desc', 'theme_obin');
    $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_TEXT);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    $name = 'theme_obin/footeremail';
    $title = get_string('footeremail', 'theme_obin');
    $description = get_string('footeremail_desc', 'theme_obin');
    $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_NOTAGS);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    $name = 'theme_obin/footerlinks';
    $title = get_string('footerlinks', 'theme_obin');
    $description = get_string('footerlinks_desc', 'theme_obin');
    $setting = new admin_setting_configtextarea($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Deux blocs de contenu libre en deux colonnes, sous la liste des cours
    // available on the front page (non-logged-in visitors). Empty by
    // default: the theme is a reusable template for any
    // organisation; content must not assume a specific association or
    // organisme en particulier.

    // Réseaux sociaux : une URL par ligne, détection automatique du réseau.
    $name = 'theme_obin/footersocial';
    $title = get_string('footersocial', 'theme_obin');
    $description = get_string('footersocial_desc', 'theme_obin');
    $setting = new admin_setting_configtextarea($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    $name = 'theme_obin/frontblock1';
    $title = get_string('frontblock1', 'theme_obin');
    $description = get_string('frontblock1_desc', 'theme_obin');
    $setting = new admin_setting_configtextarea($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    $name = 'theme_obin/frontblock2';
    $title = get_string('frontblock2', 'theme_obin');
    $description = get_string('frontblock2_desc', 'theme_obin');
    $setting = new admin_setting_configtextarea($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Section "chiffres clés" affichée entre la bannière et la liste des
    // cours (visiteurs non connectés uniquement) : nombre de cours,
    // d'apprenants et d'activités, calculés automatiquement depuis la base
    // Moodle (voir theme_obin_get_frontpage_stats() dans lib.php). Activée
    // par défaut : un site qui vient d'installer le thème a en général
    // au moins un cours à montrer ; l'admin peut la masquer ici si les
    // chiffres ne sont pas encore assez parlants (site tout juste créé).
    $name = 'theme_obin/showstats';
    $title = get_string('showstats', 'theme_obin');
    $description = get_string('showstats_desc', 'theme_obin');
    $setting = new admin_setting_configcheckbox($name, $title, $description, '1');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);
}

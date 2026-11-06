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
 * Layout de la page d'accueil du thème OBIN.
 *
 * Copie du layout "drawers" de Boost (thème parent), à l'identique, à
 * l'exception du template rendu en sortie : "theme_obin/frontpage" au lieu
 * de "theme_boost/drawers". Ce template ajoute uniquement une bannière
 * (photo + slogan officiel de la charte graphique) au-dessus du contenu
 * habituel de la page d'accueil (résumé du site + cours disponibles),
 * sans rien changer à ce contenu ni aux autres pages du site (qui
 * continuent d'utiliser les layouts de Boost, cf. theme_obin/config.php).
 *
 * @package    theme_obin
 * @copyright  2026 Citoyenneté et Inclusion Numérique (OBIN) <contact@obin-asso.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/behat/lib.php');
require_once($CFG->dirroot . '/course/lib.php');

// Add block button in editing mode.
$addblockbutton = $OUTPUT->addblockbutton();

if (isloggedin()) {
    $courseindexopen = (get_user_preferences('drawer-open-index', true) == true);
    $blockdraweropen = (get_user_preferences('drawer-open-block') == true);
} else {
    $courseindexopen = false;
    $blockdraweropen = false;
}

if (defined('BEHAT_SITE_RUNNING') && get_user_preferences('behat_keep_drawer_closed') != 1) {
    $blockdraweropen = true;
}

$extraclasses = ['uses-drawers'];
if ($courseindexopen) {
    $extraclasses[] = 'drawer-open-index';
}
// Dedicated CSS class (instead of "pagelayout-frontpage", too broad): only targets
// the case where the banner is actually shown, so as not to hide the
// page title or collapse the top spacing when a logged-in user
// visits this page anyway (the banner itself is never shown
// dans ce cas - cf. obinheroshown plus bas).
if (!isloggedin() || isguestuser()) {
    $extraclasses[] = 'obin-hero-active';
}

$blockshtml = $OUTPUT->blocks('side-pre');
$hasblocks = (strpos($blockshtml, 'data-block=') !== false || !empty($addblockbutton));
if (!$hasblocks) {
    $blockdraweropen = false;
}
$courseindex = core_course_drawer();
if (!$courseindex) {
    $courseindexopen = false;
}

$bodyattributes = $OUTPUT->body_attributes($extraclasses);
$forceblockdraweropen = $OUTPUT->firstview_fakeblocks();

$secondarynavigation = false;
$overflow = '';
if ($PAGE->has_secondary_navigation()) {
    $tablistnav = $PAGE->has_tablist_secondary_navigation();
    $moremenu = new \core\navigation\output\more_menu($PAGE->secondarynav, 'nav-tabs', true, $tablistnav);
    $secondarynavigation = $moremenu->export_for_template($OUTPUT);
    $overflowdata = $PAGE->secondarynav->get_overflow_menu_data();
    if (!is_null($overflowdata)) {
        $overflow = $overflowdata->export_for_template($OUTPUT);
    }
}

$primary = new core\navigation\output\primary($PAGE);
$renderer = $PAGE->get_renderer('core');
$primarymenu = $primary->export_for_template($renderer);
$buildregionmainsettings = !$PAGE->include_region_main_settings_in_header_actions() && !$PAGE->has_secondary_navigation();
// If the settings menu will be included in the header then don't add it here.
$regionmainsettingsmenu = $buildregionmainsettings ? $OUTPUT->region_main_settings_menu() : false;

$header = $PAGE->activityheader;
$headercontent = $header->export_for_template($renderer);

$obinheroimageurls = theme_obin_get_hero_images();
$obinheroimages = array_map(function($url, $index) {
    return ['url' => $url, 'first' => ($index === 0)];
}, $obinheroimageurls, array_keys($obinheroimageurls));

$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
    'output' => $OUTPUT,
    'sidepreblocks' => $blockshtml,
    'hasblocks' => $hasblocks,
    'bodyattributes' => $bodyattributes,
    'courseindexopen' => $courseindexopen,
    'blockdraweropen' => $blockdraweropen,
    'courseindex' => $courseindex,
    'primarymoremenu' => $primarymenu['moremenu'],
    'secondarymoremenu' => $secondarynavigation ?: false,
    'mobileprimarynav' => $primarymenu['mobileprimarynav'],
    'usermenu' => $primarymenu['user'],
    'langmenu' => $primarymenu['lang'],
    'forceblockdraweropen' => $forceblockdraweropen,
    'regionmainsettingsmenu' => $regionmainsettingsmenu,
    'hasregionmainsettingsmenu' => !empty($regionmainsettingsmenu),
    'overflow' => $overflow,
    'headercontent' => $headercontent,
    'addblockbutton' => $addblockbutton,
    // OBIN front-page banner: image(s) + title + tagline, all configurable
    // from Site administration > Appearance > OBIN. Admin-uploaded image(s)
    // take priority; falls back to the image bundled with the theme. With
    // two or more images, the banner becomes an auto-advancing slideshow
    // (see theme_obin_get_hero_images() in lib.php and the JS at the
    // bottom of templates/frontpage.mustache).
    'obinheroimages' => $obinheroimages,
    'obinheroslideshow' => count($obinheroimageurls) > 1,
    'obinheroheading' => format_string(get_config('theme_obin', 'heroheading') ?: 'Citoyenneté et Inclusion Numérique'),
    'obinherosubheading' => format_string(
        get_config('theme_obin', 'herosubheading') ?: 'Chaque citoyen mérite d\'être acteur de sa vie numérique.'
    ),
    'obinheroshown' => !isloggedin() || isguestuser(),
    // Chiffres clés affichés sous la bannière (nombre de cours, d'apprenants,
    // d'activités, + un 4e chiffre choisi automatiquement - voir
    // theme_obin_get_frontpage_stats() dans lib.php), pour les visiteurs non
    // connectés uniquement. Réglage "theme_obin/showstats" pour les masquer.
    'obinshowstats' => !empty(get_config('theme_obin', 'showstats')),
    'obinstats' => theme_obin_get_frontpage_stats(),
    // Mini-formulaire de connexion directement dans le menu (au lieu de
    // rediriger vers /login/index.php) : jeton anti-CSRF requis par
    // authenticate_user_login() (cf. \core\session\manager::validate_login_token()),
    // valid for the current session regardless of the page that generates it.
    'obinloginurl' => (new moodle_url('/login/index.php'))->out(false),
    'obinlogintoken' => \core\session\manager::get_login_token(),
    // Deux blocs de contenu libre sous la liste des cours, personnalisables
    // from Site administration > Appearance > OBIN (theme = template,
    // content depends entirely on the deploying organisation – therefore
    // empty by default, nothing hard-coded). Basic sanitised HTML (format_text)
    // rather than plain text, to allow at minimum paragraph breaks.
    'obinfrontblock1' => theme_obin_format_frontblock(get_config('theme_obin', 'frontblock1')),
    'obinfrontblock2' => theme_obin_format_frontblock(get_config('theme_obin', 'frontblock2')),
    'obinshowfrontblocks' => !empty(get_config('theme_obin', 'frontblock1')) || !empty(get_config('theme_obin', 'frontblock2')),
    // "Create account" button: only shown when self-registration is
    // actually available on this site (same check as Moodle core
    // Moodle sur la page de connexion), pour ne jamais pointer vers un
    // with registration form disabled.
    'obinshowsignup' => theme_obin_signup_available(),
];

echo $OUTPUT->render_from_template('theme_obin/frontpage', $templatecontext);

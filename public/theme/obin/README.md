# Thème Moodle OBIN

Thème enfant de **Boost** (le thème par défaut de Moodle depuis la version 3.2) pour l'association loi 1901 **Citoyenneté et Inclusion Numérique (OBIN)**, engagée pour l'inclusion numérique, la reconnaissance des compétences par Open Badges et un usage éthique de l'IA.

> « Chaque citoyen mérite d'être acteur de sa vie numérique. »

Ce thème applique la charte graphique officielle de l'association (couleurs, typographie, quelques détails d'accessibilité) par-dessus les gabarits de Boost, et ajoute quelques fonctionnalités optionnelles pour la page d'accueil (bandeau photo/diaporama, section "chiffres clés"), tout en restant entièrement compatible avec les mises à jour futures de Boost.

## Fonctionnalités

- Palette officielle : bleu OBIN `#3D8FE8`, vert-teal `#64D6A8`, cyan accent `#4CBFD1`.
- Typographie : Poppins pour les titres, Lato pour le corps de texte (chargées depuis Google Fonts).
- Dégradé de marque bleu → teal appliqué à la barre de navigation et aux boutons principaux, conformément à la charte ("réservé aux éléments graphiques forts").
- Indicateur de focus clavier renforcé (accessibilité), cohérent avec la mission d'inclusion numérique de l'association.
- Couleurs primaire/secondaire personnalisables depuis *Administration du site > Apparence > OBIN*, avec les couleurs de la charte comme valeurs par défaut.
- Champs SCSS bruts (avant/après compilation) pour toute personnalisation avancée, sur le même principe que le thème Boost.
- Icônes des menus d'administration configurables depuis *Administration du site > Apparence > OBIN* : aucune (texte seul, comportement natif de Boost), emoji (apparence historique du thème) ou pictogrammes SVG dessinés pour ce thème (monochromes, suivent la couleur de marque).
- Bannière de page d'accueil personnalisable (photo, titre, sous-titre) : une ou plusieurs photos peuvent être déposées, avec un diaporama automatique en fondu enchaîné à partir de deux photos.
- Section "chiffres clés" affichée sous la bannière (nombre de cours, d'apprenants, d'activités, et un 4e chiffre choisi automatiquement selon ce que le site utilise réellement : badges délivrés, taux de réussite ou nombre de formateurs), activable/désactivable depuis les réglages.

## Installation

1. Télécharger ou cloner ce dépôt dans `[racine Moodle]/theme/obin`.
2. Se connecter en administrateur et se rendre sur *Administration du site > Notifications* pour terminer l'installation.
3. Activer le thème depuis *Administration du site > Apparence > Thèmes > Sélecteur de thème*.
4. Le logo se configure normalement, depuis *Administration du site > Apparence > Logos* (ce thème n'a pas de réglage de logo propre, il respecte le réglage natif de Moodle). Tant qu'aucun logo n'y est déposé, le thème affiche son propre logo par défaut (embarqué dans `pix/logo.png`) sans jamais écrire dans ce réglage global : le désactiver ou activer un autre thème ne laisse donc aucune trace.

## Compatibilité

Moodle 4.0 et supérieur (thème enfant de Boost).

## Licence

Ce thème est distribué sous licence **GNU GPL v3 ou ultérieure**, comme l'ensemble de l'écosystème Moodle. Voir le fichier [LICENSE](LICENSE).

## Auteur

**Citoyenneté et Inclusion Numérique (OBIN)** — association loi 1901
[obin-asso.fr](https://obin-asso.fr) — contact@obin-asso.fr

Ce thème peut être librement utilisé, modifié et redistribué sous les termes de la GPL. Si vous appréciez ce travail, vous pouvez soutenir l'association via [HelloAsso](https://www.helloasso.com/associations/citoyennete-et-inclusion-numerique-obin/formulaires/4) — entièrement facultatif, aucune fonctionnalité du thème n'en dépend.

# Nelson Hub

Compagnon de vie personnel construit avec **Symfony + PWA**, accompagné d'une interface statique déployable sur **GitHub Pages**.

## Objectif

Centraliser au même endroit :

- résultats et prochains matchs de FC Fleury 91, RC Lens, Racing CFF et SC Braga ;
- transports Île-de-France via PRIM / Île-de-France Mobilités ;
- trains SNCF / Navitia ;
- contrôle de la maison via Home Assistant ;
- calendrier sportif et personnel ;
- trois profils utilisateurs distincts ;
- installation PWA et future enveloppe iOS / `.ipa` via Capacitor.

## Profils

La V1 comporte trois profils : `Nelson`, `Profil 2` et `Profil 3`. Ils sont renommables.

- Sur GitHub Pages, les noms et le profil actif sont enregistrés dans le navigateur via `localStorage`.
- Sur Symfony, le profil actif et les noms sont gérés par session.
- L'étape suivante sera une persistance en base de données avec comptes/PIN et préférences séparées par utilisateur pour synchroniser plusieurs appareils.

## GitHub Pages

Le site statique se trouve dans `docs/` et est déployé par `.github/workflows/pages.yml`.

> GitHub Pages ne peut pas exécuter Symfony/PHP. La PWA statique sert d'interface immédiatement accessible, tandis que Symfony sera hébergé séparément et exposera l'API dynamique.

## Backend Symfony

Le backend recevra les connecteurs :

- API football ;
- IDFM / PRIM ;
- SNCF / Navitia ;
- Home Assistant ;
- agenda et préférences par profil.

## iPhone

La PWA est déjà installable depuis Safari. Une enveloppe Capacitor/iOS sera ajoutée pour produire un vrai projet Xcode puis un `.ipa` signé.
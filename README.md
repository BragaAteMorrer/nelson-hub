# Nelson Hub

Compagnon de vie personnel en **Symfony + PWA**, pensé pour trois personnes du foyer.

## Comptes réellement indépendants

Il n'y a plus de "profils locaux" partagés dans le navigateur.

Chaque personne possède :

- son propre e-mail de connexion ;
- son propre mot de passe hashé ;
- sa propre session ;
- son nom affiché ;
- son thème, accent, langue et fuseau horaire ;
- ses équipes suivies ;
- ses notifications sportives ;
- ses arrêts IDFM favoris ;
- ses gares SNCF favorites ;
- ses appareils Home Assistant favoris ;
- ses préférences calendrier.

Aucun utilisateur ne modifie les réglages d'un autre via l'interface normale.

## GitHub Pages

GitHub Pages sert uniquement de **coque PWA publique**. Il ne peut pas exécuter Symfony/PHP et ne contient aucun mot de passe, token API ou donnée privée.

L'erreur `Resource not accessible by integration` vient du fait que le token GitHub Actions n'a pas le droit d'activer Pages lui-même.

À faire **une seule fois** dans le repo :

1. **Settings**
2. **Pages**
3. Dans **Build and deployment**
4. Source : **GitHub Actions**
5. Puis relancer **Deploy GitHub Pages**

Le workflow a été corrigé pour ne plus tenter d'activer Pages automatiquement.

## Installation Symfony

```bash
composer install
cp .env .env.local
php bin/console doctrine:migrations:migrate
```

Les secrets doivent aller dans `.env.local`, jamais dans Git.

## Créer les trois comptes

```bash
php bin/console app:user:create user1@example.com "Utilisateur 1" --admin
php bin/console app:user:create user2@example.com "Utilisateur 2"
php bin/console app:user:create user3@example.com "Utilisateur 3"
```

Le mot de passe est demandé de manière masquée et n'apparaît ni dans Git ni dans l'historique shell de la commande.

## Réglages

Une fois connecté :

- `/` : tableau de bord personnel ;
- `/settings` : personnalisation complète du compte ;
- `/settings/password` : changement de mot de passe ;
- `/api/me/settings` : lecture/modification des préférences du compte connecté.

## Connecteurs serveur

Les intégrations préparées sont :

- API-Football ;
- Île-de-France Mobilités PRIM ;
- API SNCF / Navitia ;
- Home Assistant.

Variables dans `.env.local` :

```dotenv
FOOTBALL_API_KEY=
IDFM_API_KEY=
SNCF_API_TOKEN=
HOME_ASSISTANT_URL=
HOME_ASSISTANT_TOKEN=
```

Les tokens ne doivent jamais être copiés dans `docs/` ou dans du JavaScript GitHub Pages.

## iPhone

La PWA publique est installable depuis Safari. Le backend Symfony fournira les données privées. Une enveloppe Capacitor/Xcode pourra ensuite être ajoutée pour produire un vrai `.ipa`.

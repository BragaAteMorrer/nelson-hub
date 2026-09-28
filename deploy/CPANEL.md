# Déploiement Nelson Hub sur cPanel

## Structure recommandée

Le dossier public du sous-domaine doit pointer vers :

```
/home/USER/nelson-hub/public
```

et non vers la racine du projet.

## Prérequis

- PHP 8.2 ou 8.3
- extensions: ctype, iconv, mbstring, pdo, sqlite3 ou pdo_mysql
- Composer 2
- HTTPS actif

## Installation

```bash
cd ~/nelson-hub
git clone https://github.com/BragaAteMorrer/nelson-hub.git .
composer install --no-dev --optimize-autoloader
cp .env .env.local
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console cache:clear --env=prod
php bin/console cache:warmup --env=prod
```

Puis édite `.env.local` avec les vraies valeurs :

```dotenv
APP_ENV=prod
APP_SECRET=UNE_LONGUE_CHAINE_ALEATOIRE
DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db"

FOOTBALL_API_KEY=
IDFM_API_KEY=
SNCF_API_TOKEN=
HOME_ASSISTANT_URL=
HOME_ASSISTANT_TOKEN=
```

Ensuite ouvre :

```
https://ton-domaine/setup
```

et crée les 3 comptes. Une fois les comptes créés, `/setup` redirige automatiquement vers la connexion.

## Permissions

```bash
chmod -R ug+rwX var
```

## Sécurité

- ne jamais exposer `.env.local`
- ne jamais mettre les clés API dans GitHub Pages
- document root = `public/`
- HTTPS obligatoire

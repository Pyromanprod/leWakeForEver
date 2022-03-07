# Projet Le Blog de Batman
## premier projet symfony guidé pour ma formation developpeur web et web mobile


## Installation

### Cloner le projet

```
git clone https://github.com/RenaudKieffer/leWakeForEver.git
```

### Modifier les paramètres d'environnement dans le fichier .env (changer user_db, password_db, clés Google Recaptcha)

### Déplacer le terminal dans le dossier cloné
```
cd leWakeForEver
```

### Taper les commandes suivantes :

```
composer install
symfony console doctrine:database:create
symfony console make:migration
symfony console doctrine:migrations:migrate
symfony console doctrine:fixtures:load
symfony console assets:install public
```

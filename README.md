# Le wake c'est cool

## Installation


### cloner le projet

```
    git clone https://github.com/RenaudKieffer/leWakeForEver.git
```

### changer les infos de env

### déplacer le terminal dans le dossier cloné

```
cd machin
```

### Taper les commandes :

```
composer install
symfony console doctrine:database:create
symfony console make:migration
symfony console doctrine:migrations:migrate
```

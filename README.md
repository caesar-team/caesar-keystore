Caesar Keystore.

Application to storage keys

## Installation

### 1. Start Containers and install dependencies: 
On Linux/Windows:
```bash
docker-compose up -d
```
On MacOS:
```bash
docker-sync-stack start
```
### 2. Run composer install
```bash
docker-compose exec php composer install
```

### 3. Run migrations, install required default fixtures
```bash
docker-compose exec php bin/console doctrine:migrations:migrate
```

### 4. Open project
Just go to [http://localhost:8090](http://localhost:8090)


Useful commands and shortcuts
==========

## Shortcuts
It is recommended to add short aliases for the following frequently used container commands:

* `docker-compose exec php php` to run php in container
* `docker-compose exec php composer` to run composer
* `docker-compose exec php bin/console` to run Symfony CLI commands

## Checking code style and running tests
Fix code style by running PHP CS Fixer:
```bash
docker-compose exec php vendor/bin/php-cs-fixer fix
```


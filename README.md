# ExerciceMMF

## Front

The front is made in ReactJs with the template: `https://github.com/rafaelhz/react-material-admin-template`

## Back

The back is made in Symfony from `https://github.com/symfony/skeleton`

### Installation
1 - Install symfony and required packages: `composer install && composer update`
2 - Update database credentials in .env.sample file and mv to .env<br>
3 - Run migrations: `php bin/console doctrine:schema:update --force`
4 - Install api platform: `yarn add --dev @symfony/webpack-encore && yarn add webpack-notifier --dev && yarn encore dev`

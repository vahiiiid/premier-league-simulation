## Document For Running Project

it is a premier league simulator with just 4 team, implemented in laravel framework
 to run the project after cloning please do the below steps:

- composer install
- php -r "file_exists('.env') || copy('.env.example', '.env');"
- create a mysql database and add your database access in .env
- php artisan key:generate 
- php artisan migrate --seed
- run the unit tests "/vendor/bin/phpunit"
- php artisan serve

then go to served url and see the project.
project features:
- generate random weekly match
- simulate each week match
- feature to simulate all the weeks immediately
- reset all played match and draw a new weeks and standings
- predict champions with current team rank
- set higher chance to win the match for home teams also including current ranking impact

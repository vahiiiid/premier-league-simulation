# Premier League Simulation

A fully featured, Lightweight premier league fixture and result simulator powered by Laravel 

list of features:

| Feature                                  | Status        |
| ---------------------------------------- | ------------- |
| Random weekly match                      | &#9745;       |
| Simulate each week separately            | &#9745;       |
| Simulate all weeks at once               | &#9745;       |
| Reset all played match                   | &#9745;       |
| Predict champion                         | &#9745;       |
| Different win chance for teams           | &#9745;       |
| Home and Away fixtures draw              | &#9745;       |
| Increasing number of teams (default 4)   | &#9745;       |
| edit played match result                 | &#9744;       |

## Getting Started

Clone the project:

```
> git clone https://github.com/vahiiiid/premier-league-simulation.git
```

### Prerequisites

for running the project you need the minimum requirement of running laravel 5.8 and there is no other third party packages


### Installing

for installing just do below steps after cloning:

```
> navigate to premier-league-simulation
> composer install
> php -r "file_exists('.env') || copy('.env.example', '.env');"
> create a mysql database and add your database access in .env
> php artisan key:generate
> php artisan migrate --seed
```


## Running the tests

this project includes unit tests for services such as prediction, simulator and fixtureDrawer

```
> vendor/bin/phpunit
```
now your project is ready to serve:

```
> php artisan serve
```


## Live Demo

* [click to see the deployed project](http://vahidvahedi.ir)


## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details


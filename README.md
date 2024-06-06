# Badging machine web interface
* Badging machine code : https://github.com/OrifInformatique/Timbreuse
* Web application code : https://github.com/OrifInformatique/timbreuse-srv

This projects consists in one or several badging machines constructed with a Raspberry, a RFID badge sensor and a touchscreen all in a box. The application on these machines is written in Pyhton and their datas are sychronized with a central web application developed in PHP with the CodeIgniter 4 framework.
The web application lets users see and modify theirs badging datas. They can also indicate their absence or vacation dates.

## Getting Started

**For the web application only**

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes. See deployment for notes on how to deploy the project on a live system.

### Prerequisites

* PHP 8.0 or newer
* Composer
* An empty database

### Installing

After cloning this git repository, make a copy of the "env_dist" file and rename it to ".env". Adjust the content of this file to match your development environment.

Get the required packages with a composer command :

```shell
composer install
```

Generate the database structure with spark :

```shell
php spark migrate --all
```

You should be able to run the application and login with admin => admin1234.

## Running the tests

We provide a partial PHPUnit test set wich can be run with this command :

```shell
vendor/bin/phpunit
```

## Deployment

Use the last release to deploy the project and follow the same steps on your production environment as described for a development environment.

## Built With

* [CodeIgniter](https://www.codeigniter.com/) - PHP framework
* [Bootstrap](https://getbootstrap.com/) - To simplify views design

## Authors

* **Orif, domaine informatique** - *Initiating and following the project* - [GitHub account](https://github.com/OrifInformatique)

See also the list of [contributors](https://github.com/OrifInformatique/timbreuse-srv/contributors) who participated in this project.

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details

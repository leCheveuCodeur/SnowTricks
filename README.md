# SnowTricks [![Codacy Badge](https://app.codacy.com/project/badge/Grade/b8ee7bd11b874407851ae3f54c2d712b)](https://www.codacy.com/gh/leCheveuCodeur/SnowTricks/dashboard?utm_source=github.com&utm_medium=referral&utm_content=leCheveuCodeur/SnowTricks&utm_campaign=Badge_Grade)

## Description

This project is the 6th project of the [Developer PHP / Symfony](https://openclassrooms.com/fr/paths/59-developpeur-dapplication-php-symfony) formation of [Openclassrooms](https://openclassrooms.com/).

The goal of this project is to create a collaborative website with the [Symfony](https://symfony.com/https://) framework.

I chose on this project to push the collaborative model to the level of Wikipedia.

Where it was required that each person can create and modify his own articles, I added the possibility for third parties to contribute on any article.
The author will have the role to validate the contribution or not.
And the Admin of the site will only validate any contribution of a new artilce.

## How it work

![Snowtricks](SnowTricks.gif)

## Build with

### Server :

- [PHP v7.4.19](https://www.php.net/releases/index.php)
- [Apache v2.4.48](https://www.apachelounge.com/download/VC15/)

* [MySQL v8.0.24](https://downloads.mysql.com/archives/installer/)
* **Server** : *for the server you can turn to the classics: [WAMP](https://www.wampserver.com/), [MAMP](https://www.mamp.info/en/downloads/), [XAMPP](https://www.apachefriends.org/fr/index.html) ...Or test the best of the swiss knives server: [Laragon](https://laragon.org/), my favorite ❤️*

### Framework & Libraries :

- [Symfony 5.3.3](https://symfony.com/https://)

* [Composer](https://getcomposer.org/download/)
* [Symfonycast/Verify-Email-Bundle v1.5.0](https://packagist.org/packages/symfonycasts/verify-email-bundle)
* [Symfonycast/Reset-Password-Bundle v1.9.1](https://packagist.org/packages/symfonycasts/reset-password-bundle)
* [FakerPHP/Faker v1.15.0](https://packagist.org/packages/fakerphp/faker)
* [Bootstrap v5.1.0](https://getbootstrap.com/)

## Installation

### **Clone or download the repository**, and put files into your environment,

```
https://github.com/leCheveuCodeur/SnowTricks.git
```

### Install libraries with **composer**,

```
composer install
```

### Configure your environment with `.env` file :

```
###> symfony/mailer https://symfony.com/doc/current/mailer.html#transport-setup ###
# MAILER_DSN=smtp://localhost
###< symfony/mailer ###

# DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=8"


###> Admin configuration, used in fixtures loading ###
ADMIN_PSEUDO='Admin'
ADMIN_EMAIL='your@email.com'
ADMIN_PASSWORD='yourPassword'

###> Email for contact ###
EMAIL_CONTACT='mailer@your-domain.com'
```

### Initialise your Database :

1 - create your database :

````
php bin/console d:d:c
````

2 - create the structure in your database :

```
php bin/console d:m:m
```

3 - and install fixturesfor have first contents and your Admin account :

```
php bin/console d:f:l -n
```

### And Voilà !

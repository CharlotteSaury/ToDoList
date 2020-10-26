# ToDoList

Project 8 of OpenClassrooms "PHP/Symfony app developper" course.

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/e56deac0c32a413785d136ddabc37762)](https://www.codacy.com/gh/CharlotteSaury/ToDoList/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=CharlotteSaury/ToDoList&amp;utm_campaign=Badge_Grade)
<img src="https://travis-ci.com/CharlotteSaury/ToDoList.svg?branch=main" alt="TravisCI badge" />

## Description

ToDo & Co is a new startup developping an application to manage life daily tasks. However, application has been developped very quickly to make demonstration to potential investors, as minimum viable product.
This project aims to implement new functionnalities, fix few anomalies and improve application quality.
Among them:
    - Improve performance and quality of outdated application
    - Implement relation between task and user
    - Add user role managment 
    - Implement authorizations restrictions
    - Implement unit and funcionnal tests to obtain a test-coverage > 70%
    - Generate quality and performance audit after app improvment
    - Suggest an improvment plan for further development

## Environment : Symfony 5 project

Project require:
* [Composer]("https://getcomposer.org/")
* PHP 7.4

## Installation

#### 1 - Git clone the project
`https://github.com/CharlotteSaury/ToDoList.git`

#### 2 - Install libraries
`php bin/console composer install`

#### 3 - Create database
* a) Update DATABASE_URL .env file with your database configuration.
    `DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name`
* b) Create database: 
    `php bin/console doctrine:database:create`
* c) Create database structure:
    `php bin/console doctrine:schema:update --force`
* d) Insert fictive data
    `php bin/console doctrine:fixtures:load --group=UserFixtures --group=TaskFixtures`

#### 4 - Start server
`symfony serve -d`

#### 5 - Open ToDoList app
`symfony open:local`

## Usage

You can now use this app.
If you generated fixtures, here are the users you can use:
* User: username: "user1" - password: "password"
* Administrator: Nom d'utilisateur: "admin1" - Mot de passe: "password"

## Testing

Unit and functionnal tests have been implemented with PHPUnit and require LiipTestFixturesBundle.
To run all tests:
`php bin/phpunit`

To run specific tests:
`php bin/phpunit --filter TaskControllerTest`

To generate up-to-date test-coverage:
`php bin/phpunit --coverage-html public/test-coverage`
Test-coverage then accessible on /public/test-coverage/index.html

## Contributing

Please refer to contributing.md

## Documentation

* UML diagrams: /documentation/UML
* Authentication guide: /documentation
* Quality and performance audit: /documentation

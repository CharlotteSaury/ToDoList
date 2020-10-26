# ToDo&Co - Contributing guidelines

If you wish to contribute to improve this project, your contribution is welcome ! 
Please follow the following guidelines.

## Prerequisites

Please install the project in local by following [README.md](README.md) instructions.

## About Symfony

This project is developed with Symfony Framework. Please refere to [Symfony documentation](https://symfony.com/doc/current/index.html) to follow best development practices.

## Code Quality

* Code quality is monitored by Codacy, as you can see the Grade A badge in [README.md](README.md) file.
* Codacy will automatically analyze your Pull Request for code quality
* Please ensure to maintain a Grade A quality level
* Please also run the following command to be sure to respect minimum PSR requirements ([PSR-1](https://www.php-fig.org/psr/psr-1/), [PSR-12](https://www.php-fig.org/psr/psr-12/) and [PSR-4](https://www.php-fig.org/psr/psr-4)): 

`vendor/bin/php-cs-fixer fix --diff --dry-run`


## Testing

Unit and functionnal tests are implemented with PHPunit and automated through TravisCI tool on Github.

#### Continuous integration

* Each time you push a commit or Pull Request, TravisCI will automatically test your code.
* Please do not modify the `.env.test` file. Instructions about test database configuration for local tests are described in the following section.
* Code coverage is >70%. Please ensure this high test coverage level.

#### PHPUnit in local

* Note that you will not be able to merge a PR ig TravisCI analysis failed, so we highly recommend to test your code in local before pushing in to the repository.
* To launch tests in your local machine, you need a test database. As mentionned above, please do not modify `.env.test` file.
* Create a '.env.test.local' file where you can configure your DATABASE_URL environment variable with your local db credentials.
* Create your test database environment by running:

```console
php bin/console doctrine:database:create --env=test
php bin/console doctrine:schema:update --force --env=test
```

* To run all tests:
```console
php bin/phpunit
```

* To run specific tests:
```console
php bin/phpunit --filter SpecificTest
```

## Instructions

* First, look at the [Project improvment plan](https://github.com/CharlotteSaury/ToDoList/projects/2) to see suggested application improvment
* If your contribution is not included in existing issues, please create a new issue to discuss around it.
* The project deployment branch is "main", so please never commit on it. Please commit on "develop" branch instead.
* After project installation in your local machine, create a new branch following nomenclature:
    - 'fix/': for modifications/bugs
    - 'feature/': for new feature
* Work on your own branch. Do not forget to implement your own tests to test your code.
* Check for code quality and check that your code passed all tests.
* Commit your changes: `git commit -am "add some feature"`
* Push your branch: `git push origin feature/my-feature"`
* If Codacy or TravisCI analyses failes, please fix your bugs, commit and push it.


# Thank you for contributing ! :)
# ToDoList

Project 8 of OpenClassrooms "PHP/Symfony app developper" course.

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/e56deac0c32a413785d136ddabc37762)](https://www.codacy.com/gh/CharlotteSaury/ToDoList/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=CharlotteSaury/ToDoList&amp;utm_campaign=Badge_Grade)
<img src="https://travis-ci.com/CharlotteSaury/ToDoList.svg?branch=main" alt="TravisCI badge" />

# Description

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

# Environment : Symfony 5 project
Project require:
<ul>
    <li><a href="https://getcomposer.org/">Composer</a></li>
    <li>PHP 7.4</li>
</ul>

# Installation

<p><strong>1 - Git clone the project</strong></p>
<pre>
    <code>https://github.com/CharlotteSaury/ToDoList.git</code>
</pre>

<p><strong>2 - Install libraries</strong></p>
<pre>
    <code>php bin/console composer install</code>
</pre>

<p><strong>3 - Create database</strong></p>
<ul>
    <li>a) Update DATABASE_URL .env file with your database configuration.
        <pre>
            <code>DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name</code>
        </pre>
    </li>
    <li>b) Create database: 
        <pre>
            <code>php bin/console doctrine:database:create</code>
        </pre>
    </li>
    <li>c) Create database structure:
        <pre>
            <code>php bin/console doctrine:schema:update --force</code>
        </pre>
    </li>
    <li>d) Insert fictive data
        <pre>
            <code>php bin/console doctrine:fixtures:load --group=UserFixtures --group=TaskFixtures</code>
        </pre>
    </li>
</ul>

<p><strong>4 - Start server</strong></p>
<pre>
    <code>symfony serve -d</code>
</pre>

<p><strong>5 - Open ToDoList app</strong></p>
<pre>
    <code>symfony open:local</code>
</pre>

# Usage

You can now use this app.
If you generated fixtures, here are the users you can use:
<ul>
    <li>Utilisateur: Nom d'utilisateur: "user1" - Mot de passe: "password"</li>
    <li>Administrateur: Nom d'utilisateur: "admin1" - Mot de passe: "password"</li>
</ul>

# Testing

Unit and functionnal tests have been implemented with PHPUnit and require LiipTestFixturesBundle.
To run all tests:
<pre>
    <code>php bin/phpunit</code>
</pre>

To run specific tests:
<pre>
    <code>php bin/phpunit --filter TaskControllerTest</code>
</pre>

# Documentation

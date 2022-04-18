# Cogitech task

A simple website presenting an example solution of the given task.

![homepage img](https://raw.githubusercontent.com/kabix09/CogitechTask/main/public/img/homepage.PNG)

<!-- ![homepage](https://github.com/kabix09/CogitechTask/tree/main/public/img/homepage.PNG?raw=true) -->

Contents
========
 * [Technologies](#technologies)
 * [Get started](#get-started)
 * [Usage](#usage)
 * [Usage examples](#usage-examples)
  
## Technologies
* [php 7.4](https://www.php.net/)
* [Symfony 6](https://react-redux.js.org/)
* [MsSQL server](https://www.microsoft.com/pl-pl/sql-server/sql-server-2019)
* [Bootstrap](https://getbootstrap.com/)
* [Api Platform](https://api-platform.com/)

## Get started
1. Download the [latest version](https://github.com/kabix09/CogitechTask)

2. Install project dependencies with [`composer`](https://getcomposer.org/) and [`yarn`](https://yarnpkg.com/) 
    * `$ composer install`
    * `$ yarn install`

3. Configure `.env` file, setup `DATABASE_URL` variable
## Usage
To start the server, from command line simply run:
```shell script
$ symfony server:start
```
First generate the database schema
```shell script
$ php bin/console doctrine:migrations:migrate
```
Next fetch posts and load to the local database using
```shell script
$ php bin/console app:load-posts
```
Finally use a browser to check whether the website is running: 
```
http://localhost:8000
```
That's it! 

## Usage examples
To log in go to:
```
http://localhost:8000/login
```
To register a new user with manager permissions go to:
```
http://localhost:8000/register
```
To get the posts list:
```
http://localhost:8000/lista
```
To get the posts **json** data run:
```
http://localhost:8000/posts
```

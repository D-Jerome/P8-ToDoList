# ToDoList

My eighth OpenClassRooms Project with PHP/Symphony.

[![Maintainability](https://api.codeclimate.com/v1/badges/ee13f5da60e8aefe708f/maintainability)](https://codeclimate.com/github/kevinmulot/OC-P8-Todolist/maintainability)
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/4bb2a9a45a5041a084d04b77d660116d)](https://www.codacy.com/gh/kevinmulot/OC-P8-Todolist/dashboard?utm_source=github.com&utm_medium=referral&utm_content=kevinmulot/OC-P8-Todolist&utm_campaign=Badge_Grade)
![OenClassRooms](https://img.shields.io/badge/OpenClassRooms-DA_PHP/SF-blue.svg)
![Project](https://img.shields.io/badge/Project-8-blue.svg)

---

## Installation

### Prerequisites

Install Composer (<https://getcomposer.org>) \

Symfony 6.4 requires PHP 8.1 or higher to run.\
Prefer MySQL 8.0.

### Download

[![Repo Size](https://img.shields.io/github/repo-size/D-Jerome/P8-ToDoList?label=Repo+Size)](https://github.com/D-Jerome/P8-ToDoList) \
Execute the following command line to download the project into your chosen directory :

```shell
git clone https://github.com/D-Jerome/P8-ToDoList.git
```

Install dependencies by running the following command :

```shell
composer install
```

### Database

Set your database connection in the **.env** file (l.28).

```shell
DATABASE_URL=mysql://username:password@127.0.0.1:3306/todo?serverVersion=8.0
```

Create database:

```shell
php bin/console doctrine:database:create
```

Build the database structure using the following command:

```shell
php bin/console doctrine:migrations:migrate
```

Load the data fixtures

```shell
php bin/console doctrine:fixtures:load
```

### Run the application

Launch the Apache/Php runtime environment by using :

```shell
php bin/console server:run
```

### Default Admin credentials

Default username `admin`\
Default password for the Admin is `password`

### Default User credentials

Default username `test`\
Default password for the user is `password`

## Support

ToDoList has continuous support !

[![Project Maintained](https://img.shields.io/maintenance/yes/2021.svg?label=Maintained)](https://github.com/kevinmulot/OC-P8-Todolist)
[![GitHub Last Commit](https://img.shields.io/github/last-commit/D-Jerome/P8-ToDoList.svg?label=Last+Commit)](https://github.com/D-Jerome/P8-ToDoList/commits/main)

## Issues

Issues can be created here.

[![GitHub Open Issues](https://img.shields.io/github/issues/D-Jerome/P8-ToDoList.svg?label=Issues)](https://github.com/D-Jerome/P8-ToDoList/issues)

## Pull Requests

Pull Requests can be created here.

[![GitHub Open Pull Requests](https://img.shields.io/github/issues-pr/D-Jerome/P8-ToDoList.svg?label=Pull+Requests)](https://github.com/D-Jerome/P8-ToDoList/pulls)

## Copyright

Code released under the MIT License.

[![GitHub License](https://img.shields.io/github/license/D-Jerome/P8-ToDoList.svg?label=License)](https://github.com/D-Jerome/P8-ToDoList/blob/main/LICENSE.md)

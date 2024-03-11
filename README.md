# ToDoList

My eighth OpenClassRooms Project with PHP/Symphony.

[![Maintainability](https://api.codeclimate.com/v1/badges/1273edde5684bf58768e/maintainability)](https://codeclimate.com/github/D-Jerome/P8-ToDoList/maintainability)
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/38ef0ae2569a40408652159623f3690c)](https://app.codacy.com/gh/D-Jerome/P8-ToDoList/dashboard?utm_source=gh&utm_medium=referral&utm_content=&utm_campaign=Badge_grade)

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

Launch symfony server by using :

```shell
symfony server:start
```

### Default Admin credentials

Default username `admin`\
Default password for the Admin is `password`

### Default User credentials

Default username `test`\
Default password for the user is `password`

## Support

ToDoList has continuous support !

[![Project Maintained](https://img.shields.io/maintenance/yes/2024.svg?label=Maintained)](https://github.com/D-Jerome/P8-ToDoList)
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

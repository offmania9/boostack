## About Boostack
Boostack is a full stack framework for PHP.
Improve your development and build your ideas

## How to get Boostack
Boostack is available open-source under the MIT license.

## Setup

**Get the latest version via Composer**

`composer create-project offmania9/boostack`

Import as vendor: composer require offmania9/boostack

## Docker Installation
Before launching Boostack installation via Docker, make sure you have set the variables found in the .env file at the project root:
    DB_DATABASE=boostack_db
    DB_PASSWORD_ROOT=ROOT
    DB_USERNAME=boostack_usr
    DB_PASSWORD=boostack_pwd

Launch Docker compose in project root

`docker-compose up -d --build`

## After you get it
Please refer to the the [Documentation](https://www.getboostack.com/docs) for additional guidance on configuring as 
* a standalone setup with PHP-Apache-MySQL 
* or within Docker containers.

## License
The Boostack framework is open-sourced software licensed under the MIT license. (https://github.com/offmania9/Boostack/blob/master/LICENSE)

## Copyright
Copyright 2014-2024 Spagnolo Stefano
@author Spagnolo Stefano <s.spagnolo@hotmail.it>
@version 6.0
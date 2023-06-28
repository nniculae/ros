# Restaurant Ordering System - Demo API

This is an incomplete API based on API Platform and Symfony.

## Requirements

- php >= 8.1
- sqlite3
- composer
- for other requirements run this command (see https://symfony.com/download)

  ```console
  $ symfony check:requirements
  ```

## Installation

- ```console
  $ git clone git@github.com:nniculae/ros.git
  or
  $ git clone https://github.com/nniculae/ros.git
  ```
- ```console
  $ cd ros
  ```
- ```console
  $ composer install
  ```
- ```console
  $  symfony console ros:install
  ```

## Run functional and unit tests

- ```console
  $ symfony php ./bin/phpunit
  ```

## Run application

- ```console
  $ symfony serve -d --no-tls
  ```
- Open your browser and go to http://localhost:8000/api/docs
- In order to manually test the application as administrator
  use the following credentials to generate a JWT Token via the endpoint /api/login_check :

  ```json
  {
    "email": "niculae@rolling.nl",
    "password": "123_secret"
  }
  ```

  Copy the token

- Click the button 'Authorize' on the top right-hand corner
- Paste the token in the field 'Value' and click on 'Authorize'

## Authors

- Niculae Niculae

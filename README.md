# Canddi Kommander
### A command line tool for handling various DevOps tasks

## Installation
- Clone this repository
- `cd src/main/php`
- Run `composer install` to install dependencies
- Run `grunt default` to build and run an initial test

## Invoking the CLI
CLI controllers can be invoked via the following command:

`./scripts/callPHP.sh -a <module>.<controller>.<action>.<verb> -p "param1=a&param2=b"`

e.g. `./scripts/callPHP.sh -a cli.hello.world.get -p "name=Logan"`

## Configuration
To edit configuration for your environment, please edit `src/main/php/Canddi/Helper/Config/config/canddi/config.ini`
To perform commands which require authentication you'll need to populate this file with a valid [Personal Access Token](https://github.com/settings/tokens) from GitHub.

## Unit testing
Unit tests can be invoked via the following command:
`./scripts/phpulocal.sh <file|directory>`

e.g. `./scripts/phpulocal.sh src/test/php/` for ALL tests

They will also be ran on a `grunt default`

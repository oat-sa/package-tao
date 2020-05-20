# Docker setup for TAO


Ready to use docker set up for working with [package-tao](https://github.com/oat-sa/package-tao)
- Php-fpm 7.2
- Nginx
- MariaDB

## Install docker

Setup local docker installation by following official documentation https://www.docker.com/products/docker-desktop and https://docs.docker.com/compose/install
Make sure ports `80` and `3306` are not occupied.


## Usage


To build and launch the docker stack execute this in the project root folder:

```
$ docker-compose up -d
```

You can then access:
- TAO on `http://localhost` on port 80
- the database on `localhost` on port 3306


## Install TAO

Install can be done via command line interface (CLI), via browser (UI) and with a configuration file.


### UI

In order to start the install wizard you should access `http://localhost` on your browser.

Database credentials can be changed in `docker-compose.yml`, default values are these:
 - Database host name: `tao_db`
 - Username for database account: `tao`
 - Password for database account: `tao`
 - Database name: `tao`


### CLI

Command line interface install parameters are described in the README.md, located in the root folder of the package.

Here is a sample CLI command to install TAO community edition using this docker stack:

```
docker exec -it --user www-data tao_phpfpm php tao/scripts/taoInstall.php \
    --db_driver pdo_mysql \
    --db_host tao_db \
    --db_name tao \
    --db_user tao \
    --db_pass tao \
    --module_namespace http://sample/first.rdf \
    --module_url http://localhost \
    --user_login admin \
    --user_pass admin \
    -vvv -e taoCe
```

### Configuration file

Documentation about install process from a configuration file can be found here: https://hub.taotesting.com/articles/installation-and-upgrading/install-the-tao-platform-from-a-configuration-file

Here is a sample CLI command to launch the setup from configuration file:

```
docker exec -it --user www-data tao_phpfpm php tao/scripts/taoSetup.php /path/to/your/configFile.json
```

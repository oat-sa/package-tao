# package-tao

> TAO Community Edition package for local development purposes.

- NGINX
- PHP-FPM
- MariaDB
- Redis

## Installation

Install dependencies:

```bash
$ composer install --prefer-source
```

This package is built on top of **OAT Docker Stack**. In order to install it, follow the installation steps in it's README file: 

[https://github.com/oat-sa/docker-stack#installation](https://github.com/oat-sa/docker-stack#installation)

Set up docker containers:

```bash
$ docker-compose up -d
```

Install TAO from seed file using PHP-FPM container:

```bash
$ docker exec -it tao-phpfpm php tao/scripts/taoSetup.php setup.json -vvv
```

Installed applications:

| Application | Port | Comments |
| :-----------|:-----|:---------|
| TAO CE | `https://tao.docker.localhost` | TAO application. Login credentials: `admin \ Test123` |

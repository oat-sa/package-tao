# Creating a custom extension

1. Create a git repo from template
2. Clone
3. cd into the repo
4. composer init
    - name the same as git repo
    - min stability: dev
    - package type: library
    - licence: proprietary
5. add package
6. composer install to make sure everything is working
7. git add
8. git commit
9. git push
10. cd ..
11. In root composer.json:

    ```json
        "repositories": [
            {
                "type": "git",
                "url": "https://github.com/yourgithubusername/taoExample.git"
            }
        ]
    ```

    ```json
        "require" : {
            // long list of other repos,
            "yourgithubusername/taoExample" : "dev-main"
        }
    ```

12. composer update

## Installation

1. Set DEBUG_MODE to true in `config/generis.conf.php`
2. Enable debug logging by adding the following to `config/generis/log.conf.php`:

    ```php
        return new oat\oatbox\log\LoggerService(
        array(
            'logger' => array(
                'class' => \oat\oatbox\log\logger\TaoMonolog::class,
                'options' => array(
                    'name' => 'tao',
                    'handlers' => array(

                        // Send log to a stream, could be a file or a daemon
                        array(
                            'class' => \Monolog\Handler\StreamHandler::class,
                            'options' => array(
                                '/var/www/html/data/log/tao.log',
                                \Monolog\Logger::DEBUG
                            ),
                        ),
                    )
                )
            )
        )
    );  
    ```

3. Log in as admin
4. Go to <http://tao.localhost/tao/Main/index?structure=settings&ext=tao&section=settings_ext_mng>
5. Your extension should be on the right side of the screen
6. Click on the extension checkbox and click install

## Docker Usage

If using Docker, run composer commands in a container that has the correct version of PHP:

```shell
docker run -it --rm --mount src=`pwd`,target=/app,type=bind --workdir /app joeniland/laravel-ci:8.1-20240612 composer update
```

When running TAO scripts, use the php-fpm container:

```shell
docker-compose exec tao_phpfpm php ./tao/scripts/taoUpdate.php
```

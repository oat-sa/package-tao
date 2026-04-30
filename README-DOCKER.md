# Docker Setup

After cloning <https://github.com/oat-sa/package-tao>:

1. (Optional) Override the host port for nginx by setting `TAO_PORT` in `.env`. Default is `8089`.

    ```sh
    echo "TAO_PORT=8089" >> .env
    ```

2. Start the stack:

    ```sh
    docker compose up -d
    ```

3. (One-time) Add a GitHub PAT so composer can clone private/rate-limited repos:

    ```sh
    ./shell.sh composer config --global github-oauth.github.com <YOUR_PAT>
    ```

    This writes `~/.composer/auth.json` on the host, which `shell.sh` mounts into the shell container.

4. Install vendor dependencies:

    ```sh
    ./shell.sh composer install
    ```

    First run builds `local/tao-shell:8.1` (PHP 8.1 + composer + git + npm + gd) — see `docker/shell/Dockerfile`. Rebuild with `docker rmi local/tao-shell:8.1` followed by another `./shell.sh` invocation.

5. Run the install script in the app container (replace `8089` if you set a different `TAO_PORT`):

    ```sh
    docker exec -it --user www-data tao_phpfpm php tao/scripts/taoInstall.php \
      --db_driver pdo_mysql \
      --db_host tao_db \
      --db_name tao \
      --db_user tao \
      --db_pass tao \
      --file_path /var/www/html/data \
      --module_namespace http://sample/first.rdf \
      --module_url http://localhost:8089 \
      --instance_name tao \
      --user_login admin \
      --user_pass admin \
      -vvv -e taoCe
    ```

6. Access the site at `http://localhost:8089` (or whatever port you configured).

## Adding extensions

For an extension hosted on a git remote (not packagist), first add a `repositories` entry to `composer.json` so composer knows where to find it:

```json
"repositories": [
  {
    "type": "git",
    "url": "https://github.com/sydneysoftwaredev/taoExtensionExcelImport.git"
  }
]
```

Then require and activate:

```sh
./shell.sh composer require sydneysoftwaredev/tao-extension-excel-import:dev-main
docker exec --user www-data tao_phpfpm \
  php /var/www/html/tao/scripts/installExtension.php taoExtensionExcelImport
```

For an extension on packagist, skip the `repositories` step — `composer require <vendor>/<name>:<constraint>` is enough.

Composer drops the extension code into `taoExtensionExcelImport/` (via `oat-sa/oatbox-extension-installer`); `installExtension.php` runs the install hooks declared in the extension's `manifest.php` and registers it with TAO.

## After any `composer update`

Clear the compiled Symfony DI container — it doesn't auto-invalidate when vendor signatures change:

```sh
docker exec --user root tao_phpfpm rm -rf /var/www/html/data/generis/cache/_di
docker restart tao_phpfpm
```

Skipping this step manifests as `ArgumentCountError` or "method not found" 500s on every request after a dependency upgrade/downgrade.

## Resetting the install

```sh
docker exec tao_db mariadb -uroot -pr00t -e "DROP DATABASE tao; CREATE DATABASE tao CHARACTER SET utf8mb4;"
docker exec --user root tao_phpfpm bash -c "rm -rf /var/www/html/data /var/www/html/config"
```

Then re-run step 5 from the install instructions above.

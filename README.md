tao-ce
======


git clone git@github.com:oat-sa/package-tao.git

git fetch origin develop:develop

git checkout develop

composer install

sudo chown www-data -R package-tao


sudo -u www-data php tao/scripts/taoInstall.php --db_driver pdo_mysql --db_host localhost --db_name taoUnitTest --db_user myuser --db_pass tao --module_host myhost  --module_namespace mynamespace --module_url myurl --user_login admin --user_pass admin -e generis,tao,taoSubjects

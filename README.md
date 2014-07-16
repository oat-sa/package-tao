tao-ce
======


git clone git@github.com:oat-sa/tao-ce.git

git fetch origin taoce_with_composer:taoce_with_composer

git checkout taoce_with_composer

composer install

sudo chown www-data -R tao-ce


sudo -u www-data php tao/scripts/taoInstall.php --db_driver pdo_mysql --db_host localhost --db_name taoUnitTest --db_user myuser --db_pass tao --module_host myhost  --module_namespace mynamespace --module_url myurl --user_login admin --user_pass admin -e generis,tao,taoSubjects

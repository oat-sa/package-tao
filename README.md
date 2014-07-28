package-tao
===========

    git clone git@github.com:oat-sa/package-tao
    composer install
    #add rw to www-data
    sudo chown www-data package-tao
    #install
    sudo -u www-data php tao/scripts/taoInstall.php --db_driver pdo_mysql --db_host localhost --db_name taoUnitTest --db_user myuser --db_pass tao --module_host myhost  --module_namespace mynamespace --module_url myurl --user_login admin --user_pass admin -e taoCe

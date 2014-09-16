package-tao
===========

Clone repository

    git clone https://github.com/oat-sa/package-tao.git
    
Install via composer missing library and extensions

    composer install
    
Add rw to www-data

    sudo chown -R www-data package-tao

Install TAO

    sudo -u www-data php tao/scripts/taoInstall.php --db_driver pdo_mysql --db_host localhost --db_name taoUnitTest --db_user myuser --db_pass tao --module_host myhost  --module_namespace mynamespace --module_url myurl --user_login admin --user_pass admin -e taoCe

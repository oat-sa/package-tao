#!/bin/sh

#sudo cp -a ultimate_rdf2db.sql /usr/lib/mysql/ultimate_rdf2db.sql

mysql -u root -p mytao < ./rdf2db_dump.sql

#sudo rm -fr /usr/lib/mysql/ultimate_rdf2db.sql

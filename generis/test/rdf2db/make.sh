#!/bin/sh

importDataTest="true"

# Get the database name
echo Please, Enter the database name :
read dbName

# create the rdf2db tables
echo CREATE HARD DB
mysql -u root --password=root $dbName < ./create_tables.sql
echo [OK]

#!/bin/bash

dbName=""
BACKUPFILE="tao_db_backup_"`date +%Y%m%d`".sql"

defineDbNameRow=`cat ../../../generis/common/config.php | grep "DATABASE_NAME" | grep "define"`
if [ "$defineDbNameRow" != "" ]; then
{
	dbName=`php -r "${defineDbNameRow}; echo DATABASE_NAME;"`
}
fi

if [ "$dbName" != "" ]; then
{
	echo "Backing up $dbName"
	mysqldump -uroot -p  --add-drop-database --add-drop-table  --databases "$dbName" > "$BACKUPFILE"

	if [ -f "$BACKUPFILE" ]; then
	{
		echo "$BACKUPFILE file created"
	}
	fi
}
else
{
	echo "Unable to retrieve the database  name from your config file"
}
fi

exit 0

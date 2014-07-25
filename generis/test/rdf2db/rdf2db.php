<?php
/*  
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php 

$dbName = "mytao";
$importDataTest = true;

// create the rdf2db tables
echo ('CREATE HARD DB ');
echo system("mysql -u root --password=root {$dbName} < ./create_tables.sql");
echo ('[OK]<br/>');

// import a test database
if ($importDataTest){
	echo ('IMPORT DATA TEST ');
	system ("mysql -u root --password=root {$dbName} < ./mytao_dump.sql");
	echo ('[OK]<br/>');	
}

///////////////////////////////////////////////////////
//	EXTRACT DATA
///////////////////////////////////////////////////////

echo ('EXTRACT SUBJECT');
echo system('mysql -u root --password=root mytao < 12Subject/extract_12Subject.sql > tmp/12Subject.txt');
echo ('[OK]<br/>');

echo ('FORMAT SUBJECT');
echo exec('rdf2db/format_csv.sh tmp/12Subject.txt tmp/12Subject.csv');

?>

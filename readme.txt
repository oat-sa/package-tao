
Linux installation procedure (or advanced windows installation)

	Use the src package version  (TAO_2.3_build.zip)
	
	1. Please install the following needed applications

	Apache server configuration :
	- recommended version 2.2.17 (other version may work)
 	- rewrite module enabled 
 	- php5 module enabled 
 	- "Allowoverride All" instruction on the DOCUMENT_ROOT 
 	- Allow www-data or apache user write permission to following files and folders :
		/ (root directory, to create the file version)
		generis/data/cache
		generis/common/conf
		filemanager/views/data
		tao/data/cache
		tao/data/serviceStorage
		tao/data/upload
		taoItems/data/itemdata
		taoDelivery/data/compiled
		taoQtiTest/data/testdata

 	PHP server configuration:
  	- required version >= 5.3 <= 5.5
  	- register_globals Off
  	- short_open_tag On
  	- magic_quotes_gpc Off
 	- required extension: mysql, mysqli, curl, json, gd, zip, spl, dom, tidy, mbstring
  
 	MySql server configuration:
  	- version >= 5.0  

	PostgreSQL
	- version >= 7.0  
	
	2. Extract the source code from the archive and install it directly in your [htdocs, www] folder
	3. Run the TAO Installation Wizard : http://localhost/tao/install/

	4. Connect to the platform using http://YOURSERVER/tao/ to manage your tests, items, etc.
	5. Connect to the platform using http://YOURSERVER/test/ to take the test as a testee

Windows installation (simple)
	
	Use the webserver package version (TAO_2.3_with_server.zip)
		
	1. Extract the archive in a directory on your filesystem.
	2. Run the file UniController.exe
	
	3. Connect to the platform using http://localhost/tao/, login: tao password: tao, to manage your tests, items, etc. 
	4. Connect to the platform using http://localhost/test/ to run a created test as a testee
	





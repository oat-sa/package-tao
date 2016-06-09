## Configuration ##

# 
# The full documentation to install or update TAO 
# is available at http://forge.taotesting.com/projects/tao/wiki/Installation_and_Upgrading
#

Apache web server configuration:
 - rewrite module enabled 
 - php5 module enabled 
 - "Allowoverride All" instruction on the DOCUMENT_ROOT 
 
 PHP server configuration:
  - required version >= 5.3
  - short_tag_open On
  - register globals Off (PHP >= 5.4)
  - magic_quotes_gpc Off (PHP >= 5.4)
  - required extensions: mysql, mysqli, curl, json, gd, zip (or compiled with zip support on Linux)
  
 MySQL server configuration:
  - version >= 5.0  
  
 
  
## INSTALL TAO ##
 - Copy TAO distribution in your web folder (the DOCUMENT ROOT of your virtual host is recommended)
 - Check the web server permissions
 - In your web browser open the page http://your-host/tao/install/ and fill out the form
 
 
 
## UPDATE AN EXISTING TAO ##
  - backup the files from the folders listed at the end this file.
  - copy the TAO distribution over the previous.
  - copy the backed-up files in their respective folders 
  - from the command line: 
  $ cd tao/install && php update.php version 
   where "version" is version to update to, for example to update from version 1.2 to 1.3 :
  $ cd tao/install && php update.php 1.3
   the process should be repeated for each intermediate version, for example to update from version 1.1 to 1.3 :
  $ cd tao/install && php update.php 1.2
  $ cd tao/install && php update.php 1.3
  
  - form the web browser(beta), you can do the same process as bellow but call the script:
  http://your-host/tao/install/update.php?version=version
   where "version" is version to update to, for example to update from version 1.2 to 1.3 :
  http://your-host/tao/install/update.php?version=1.3
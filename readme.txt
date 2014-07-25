
Installation procedure for Linux / Windows (advanced installation)

	Use the source package version (TAO_2.6_build.zip).
	
	1. Please install the following needed applications:

            Apache 2 server:
            - recommended version: 2.2.17 (other version may work)
            - rewrite module enabled
            - PHP 5 module enabled
            - "AllowOverride All" directive set for DOCUMENT_ROOT
            - Allow www-data or Apache user write permission to following files and folders:
                - / (root directory, for the .cache directory only)
                - generis/data/
                - generis/common/conf
                - filemanager/views/data
                - taoResults/views/genpics

            PHP server configuration:
                - required version >= 5.3 <= 5.5
                - register_globals Off
                - short_open_tag On
                - magic_quotes_gpc Off
                - required extensions: mysql, mysqli, curl, json, gd, zip, spl, dom, mbstring

            MySQL server configuration:
            - version >= 5.0  

            PostgreSQL
            - version >= 7.0  
	
	2. Extract the source code from the archive and install it directly in your [htdocs, www] folder
	3. Run the TAO Installation Wizard : http://YOURSERVER/tao/install/
	4. After the install completes, you are redirected to http://YOURSERVER/

Windows installation (package version)
	
	Use the web server package version (TAO_2.6_with_server.exe)

	1. Extract the archive in a directory on your filesystem.
	2. Run the file UniController.exe
	3. After the install completes, you are redirected to http://YOURSERVER/. Connect to the TAO platform using the following credentials:
            login: tao
            password: tao

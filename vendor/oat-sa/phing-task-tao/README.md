phing-task-tao
==============

set of phing tasks to deploy tao

	<taskdef name="taoinstall" classname="InstallTaoTask" />
	<typedef name="taoDbConfig" classname="TaoDbConfig" />
	<typedef name="generisConfig" classname="GenerisConfig" />
	<typedef name="taoConfig" classname="TaoConfig" />
	<taoinstall taoPath="${src.dir.resolved}" >
		<taoConfig 	
	      		login="${user.login}"
	      		pass="${user.pass}">
	      	<taoDbConfig 
	      		dbDriver="${db.driver}" 
	      		dbHost="${db.host}"
	      		dbUser="${db.user}"
	      		dbPass="${db.pass}"
	      		dbName="${db.name}"
	      	/>
	      	<generisConfig       		
	      		instanceName="${module.name}"
	      		moduleUrl="${module.url}"
	      		moduleNs="${module.namespace}"
	      		moduleMode="${module.mode}"
	      		dataPath="${src.dir.resolved}/data/"
	      		extensions="${extensions_list}"
	      	/>
	      	</taoConfig>
	</taoinstall>

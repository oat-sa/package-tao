ModeInitial = new Object();

ModeInitial.on = function(){
	return true;
}

ModeInitial.save = function(){
	//processUri
	ActivityDiagramClass.saveDiagram();
	return true;
}

ModeInitial.cancel = function(){
	return true;
}


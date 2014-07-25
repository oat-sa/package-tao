// alert('ModeController loaded');

ModeController = new Object();
ModeController.currentMode = 'ModeInitial';

ModeController.setMode = function(mode, data){
	
	
	var modeObject = null;
	if(!window[mode]){
		return false;
	}else{
		modeObject = window[mode];
	}
	
	var oldMode = ModeController.currentMode;
	
	if(oldMode && mode){
		ModeController.disable(oldMode);
		if(ModeController.enable(mode, data)){
			ModeController.currentMode = mode;
		}
	}
	
}

ModeController.disable = function(mode){
	// console.log('disabling', mode);
	if(!window[mode]){
		return false;
	}else{
		var modeObject = window[mode];
		modeObject.cancel();
		ActivityDiagramClass.unsetFeedbackMenu();
		return true;
	}
	
}

ModeController.enable = function(mode, data){
	// console.log('enabling', mode);
	if(window[mode]){
	
		if(ActivityDiagramClass.setFeedbackMenu(mode)){
			window[mode].on(data);
			return true;
		}
		
	}
	
	return false;
}

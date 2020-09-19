<?php

setDefine("MK2_SYSNAME","app");
setDefine("MK2_ROOT_LEVEL",1);
setDefine("MK2_PATH_VENDOR","../../vendor/");
setDefine("MK2_PATH_APP","../../".MK2_SYSNAME."/");
setDefine('MK2_NAMESPACE','mk2\core');
setDefine("MK2_PATH_APPCONF",MK2_PATH_APP."AppConf/");
setDefine("MK2_PATH_APPCONFINIT",MK2_PATH_APPCONF."Init/");
setDefine("MK2_PATH_WEB",MK2_PATH_APP."Web/");
setDefine("MK2_PATH_CONF",MK2_PATH_APPCONF."config.php");
setDefine("MK2_PATH_RENDERING","Rendering");
setDefine("MK2_PATH_APP_CONTROLLER",MK2_PATH_APP."Controller/");
setDefine("MK2_PATH_APP_PACKER",MK2_PATH_APP."Packer/");
setDefine("MK2_PATH_APP_SHELL",MK2_PATH_APP."Shell/");
setDefine("MK2_PATH_APP_MODEL",MK2_PATH_APP."Model/");
setDefine("MK2_PATH_APP_TABLE",MK2_PATH_APP."Table/");
setDefine("MK2_PATH_APP_VALIDATOR",MK2_PATH_APP."Validator/");
setDefine("MK2_PATH_APP_UI",MK2_PATH_APP."UI/");
setDefine("MK2_PATH_APP_RENDER",MK2_PATH_APP.MK2_PATH_RENDERING."/Render/");
setDefine("MK2_PATH_APP_VIEW",MK2_PATH_APP.MK2_PATH_RENDERING."/View/");
setDefine("MK2_PATH_APP_TEMPLATE",MK2_PATH_APP.MK2_PATH_RENDERING."/Template/");
setDefine("MK2_PATH_APP_VIEWPART",MK2_PATH_APP.MK2_PATH_RENDERING."/ViewPart/");
setDefine("MK2_PATH_APP_TEMPORARY",MK2_PATH_APP."tmp/");
setDefine("MK2_RENDERING_EXTENSION",".view");


# setDefine
function setDefine($name,$value){

	if(!defined($name)){
		define($name,$value);
	}

}
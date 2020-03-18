<?php

setDefine("MK2_SYSNAME","AP1");
setDefine("MK2_ROOT_LEVEL",1);
setDefine("MK2_PATH_ROOTREVERSE","../");
setDefine("MK2_PATH_VENDOR",MK2_PATH_ROOTREVERSE."../../vendor/");
setDefine("MK2_PATH_GLOBAL",MK2_PATH_ROOTREVERSE."../../apps/");
setDefine("MK2_PATH_APP",MK2_PATH_ROOTREVERSE."../../apps/".MK2_SYSNAME."/");
setDefine('MK2_NAMESPACE','mk2\core');
setDefine("MK2_PATH_APPCONF",MK2_PATH_APP."AppConf/");
setDefine("MK2_PATH_APPCONFINIT",MK2_PATH_APPCONF."Init/");
setDefine("MK2_PATH_WEB",MK2_PATH_APP."Web/");
setDefine("MK2_PATH_CONF",MK2_PATH_APPCONF."config.php");
setDefine("MK2_PATH_BACKEND","Backend");
setDefine("MK2_PATH_MIDDLE","Middle");
setDefine("MK2_PATH_RENDERING","Rendering");
setDefine("MK2_PATH_APP_CONTROLLER",MK2_PATH_APP.MK2_PATH_BACKEND."/Controller/");
setDefine("MK2_PATH_APP_PACKER",MK2_PATH_APP.MK2_PATH_BACKEND."/Packer/");
setDefine("MK2_PATH_APP_SHELL",MK2_PATH_APP.MK2_PATH_BACKEND."/Shell/");
setDefine("MK2_PATH_APP_MODEL",MK2_PATH_APP.MK2_PATH_MIDDLE."/Model/");
setDefine("MK2_PATH_APP_TABLE",MK2_PATH_APP.MK2_PATH_MIDDLE."/Table/");
setDefine("MK2_PATH_APP_VALIDATOR",MK2_PATH_APP.MK2_PATH_MIDDLE."/Validator/");
setDefine("MK2_PATH_APP_RENDER",MK2_PATH_APP.MK2_PATH_RENDERING."/Render/");
setDefine("MK2_PATH_APP_TEMPLATE",MK2_PATH_APP.MK2_PATH_RENDERING."/Template/");
setDefine("MK2_PATH_APP_VIEWPART",MK2_PATH_APP.MK2_PATH_RENDERING."/ViewPart/");
setDefine("MK2_PATH_APP_PLUGIN",MK2_PATH_APP."/plugin/");
setDefine("MK2_PATH_APP_TEMPORARY",MK2_PATH_APP."/tmp/");
setDefine("MK2_RENDERING_EXTENSION",".view");

# setDefine
function setDefine($name,$value){

	if(!defined($name)){
		define($name,$value);
	}

}
<?php
/**
 * AionCMS
 * https://aioncms.com
 * 
 * @author Lautaro Angelico <https://lautaroangelico.com/>
 * @copyright (c) 2012-2019 Lautaro Angelico, All Rights Reserved
 */

function base_url($return=false) {
	if($return) return __BASE_URL__; else echo __BASE_URL__;
}

function page_url($return=false) {
	if($return) return __PAGE_URL__; else echo __PAGE_URL__;
}

function module_url($module="",$return=false) {
	if($return) return __PAGE_URL__.$module; else echo __PAGE_URL__.$module;
}

function template_base($return=false) {
	if($return) return __PATH_TEMPLATE__; else echo __PATH_TEMPLATE__;
}

function template_root($return=false) {
	if($return) return __PATH_TEMPLATE_ROOT__; else echo __PATH_TEMPLATE_ROOT__;
}

function template_img($return=false) {
	if($return) return __PATH_TEMPLATE_IMG__; else echo __PATH_TEMPLATE_IMG__;
}

function template_css($return=false) {
	if($return) return __PATH_TEMPLATE_CSS__; else echo __PATH_TEMPLATE_CSS__;
}

function template_js($return=false) {
	if($return) return __PATH_TEMPLATE_JS__; else echo __PATH_TEMPLATE_JS__;
}

function template_fonts($return=false) {
	if($return) return __PATH_TEMPLATE_FONTS__; else echo __PATH_TEMPLATE_FONTS__;
}

function template_load($file) {
	if(check($file) && file_exists(template_root(true) . $file)) {
		include(template_root(true).$file);
	}
}

function template_requesturl() {
	$result = page_url(true) . $_GET['request'];
	if($return) return $result; else echo $result;
}

function message($message="",$type="") {
	if(!check($message)) return;
	switch($type) {
		case "success":
			echo '<div class="alert alert-success">'.$message.'</div>';
			break;
		case "error":
			echo '<div class="alert alert-danger">'.$message.'</div>';
			break;
		case "info":
			echo '<div class="alert alert-info">'.$message.'</div>';
			break;
		case "warning":
			echo '<div class="alert alert-warning">'.$message.'</div>';
			break;
		default:
			echo '<div class="alert">'.$message.'</div>';
	}
}
<?php
require_once 'Zend/Controller/Action/Helper/Abstract.php';

class Gam_Action_Helper_FileContent extends Zend_Controller_Action_Helper_Abstract
{
	const JS = 'js';
	const CSS = 'css';

	private function _getFiles(Gam_Controller_Action $obj, $fileType, $namespace)
	{
		$arrData = array ();
		$fileTypeVarName = strtolower ( $fileType );
		if (isset ( $obj->{$fileTypeVarName} [$namespace] ) && is_array ( $obj->{$fileTypeVarName} [$namespace] ) && count ( $obj->{$fileTypeVarName} [$namespace] )) {
			foreach ( $obj->{$fileTypeVarName} [$namespace] as $item ) {
				list ( $module, $controller, $item ) = $this->_decodeNamespace ( $fileType, $item );
				$file = GamBASEPATH . "/app/modules/{$module}/controllers/{$controller}/{$fileType}/{$item}";
				if (is_file ( $file )) {
					$arrData [] = file_get_contents ( $file );
				} else {
					throw new Exception ( "{$fileType}:{$item} not found at {$file}" );
				}
			}
		} else {
			throw new Exception ( "Namespace {$namespace} not valid" );
		}
		$out = implode ( "\n", $arrData );
		switch ($fileType) {
			case self::JS :
				$out = "var user_lang='es';var app_module='{$module}'; var app_controller='{$controller}';{$out}";
				if (Zend_Registry::get('config')->js->minify == 1) {
					$out = JSMin::minify ( $out );
				}
				break;
			case self::CSS :
				if (Zend_Registry::get('config')->css->minify == 1) {
				    $out = CSSMin::minify ( $out );
				}
				break;
		}
		return $out;
	}

	public function direct(Gam_Controller_Action $obj, $fileType, $namespace)
	{
		switch ($fileType) {
			case self::JS :
				return $this->js ( $obj, $namespace );
				break;
			case self::CSS :
				return $this->css ( $obj, $namespace );
				break;
		}
	}
	
	private function _decodeNamespace($type, $namespace)
	{
		$module = Zend_Controller_Front::getInstance()->getRequest()->getModuleName();
		$controller = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
		if (strpos ( $namespace, '/' ) !== false) {
			$fArr = explode ( '/', $namespace );
			$filename = $fArr [1];
			if (strpos ( $fArr [0], ':' ) !== false) {
				$fArr2 = explode ( ':', $fArr [0] );
				$module = $fArr2 [0];
				$controller = $fArr2 [1];
			} else
			$controller = $fArr [0];
		} else
		$filename = $namespace;
		return array ($module, ucfirst($controller), $filename );
	}

	public function js(Gam_Controller_Action $obj, $namespace)
	{
		$this->getResponse ()->setHeader ( 'Content-Type', 'application/javascript' );
		echo $this->_getFiles ( $obj, self::JS, $namespace );
			
	}

	public function css(Gam_Controller_Action $obj, $namespace)
	{
		$this->getResponse ()->setHeader ( 'Content-Type', 'text/css' );
		echo $this->_getFiles ( $obj, self::CSS, $namespace );
	}
}
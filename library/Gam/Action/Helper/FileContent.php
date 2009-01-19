<?php
require_once 'Zend/Controller/Action/Helper/Abstract.php';

class Gam_Action_Helper_FileContent extends Zend_Controller_Action_Helper_Abstract
{
    const JS = 'js';
    const CSS = 'css';

    public function js(Gam_Controller_Action $obj, $namespace)
    {
        $this->direct($obj, self::JS, $namespace);
    }

    public function css(Gam_Controller_Action $obj, $namespace)
    {
        $this->direct($obj, self::CSS, $namespace);
    }

    public function direct(Gam_Controller_Action $obj, $fileType, $namespace)
    {
        $_data = array();
        switch ($fileType) {
            case self::JS:
                $this->getResponse()->setHeader('Content-Type', 'application/javascript');
                $_data = (array) $obj->{$fileType}[$namespace];
                break;
            case self::CSS:
                $this->getResponse()->setHeader('Content-Type', 'text/css');
                $_data = (array) $obj->{$fileType}[$namespace];
                break;
        }

        if (count($_data) > 0) {
            $_arr = array();
            $module = $obj->getRequest()->getModuleName();
            $controller = $obj->getRequest()->getControllerName();
            $view = new Zend_View();
            foreach ($_data as $key => $item) {
                if (is_string($key) && $key == 'dynamic') {
                    foreach ($item as $_item) {
                        ob_start();
                        $obj->dispatch($_item);
                        $out = ob_get_contents();
                        ob_end_clean();
                        $_arr[] = $out;
                    }
                } else {
                    $view->addScriptPath(GamBASEPATH .
                        "/app/modules/{$module}/views/scripts/{$controller}/{$fileType}/");
                    $_arr[] = $view->render("{$item}");
                }
            }
            $out = implode("\n", $_arr);
            if (Zend_Registry::get('config')->{$fileType}->minify == 1) {
                switch ($fileType) {
                    case self::JS:
                        $out = JSMin::minify($out);
                        break;
                    case self::CSS:
                        $out = CSSMin::minify($out);
                        break;
                }
            }
            echo $out;
        }
    }
}
<?php
class Gam_Controller_Action extends Zend_Controller_Action
{
    protected $security = array(
	   'guest'  => 'allow',
	   'member' => 'allow',
	   'admin'  => 'allow',
	   );

	public function init()
	{
	    $this->acl($this->security);
	}
    protected function setNoRender()
    {
        $this->_helper->viewRenderer->setNoRender();
    }

    protected function exceptionMsg(Exception $e, $txt)
    {
        return (Zend_Registry::get('config')->debug == 1) ? $e->getMessage() : $txt;
    }

    protected function acl($security)
    {
        $acl = Zend_Registry::get ( 'acl' );
        $module = Zend_Controller_Front::getInstance()->getRequest()->getModuleName();
		$controller = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();

		$resource = "{$module}_{$controller}";

	    $acl->add(new Zend_Acl_Resource($resource));
	    foreach ($security as $role => $action) {
	        $acl->$action($role, $resource);
	    }
	    $acl = Zend_Registry::set ( 'acl', $acl);
    }

    private function _url2($module, $controller, $action, $params=array())
    {
        $out = "?module={$module}&controller={$controller}&action={$action}";
        if (count($params)>0) {
            foreach ($params as $key=>$value) {
                $out .= "&{$key}=" . urlencode($value);
            }
        }
        return $out;
    }

    private function _url($module, $controller, $action, $params=array())
    {
        $out = "/{$module}/{$controller}/{$action}";
        if (count($params)>0) {
            foreach ($params as $key=>$value) {
                $out .= "/{$key}/" . urlencode($value);
            }
        }
        return $out;
    }

    protected function getClientUrl($type, $namespace)
	{
	    $module = Zend_Controller_Front::getInstance()->getRequest()->getModuleName();
		$controller = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
		return $this->_url($module, $controller, $type, array('file' => $namespace));
	}

    protected function getParam($param, $default=null)
    {
        Zend_Loader::loadClass('Zend_Filter_StripTags');
        $filter = new Zend_Filter_StripTags();
        return $filter->filter($this->_getParam($param, $default));
        //return $this->_getParam($param, $default);
    }

    /**
     * @var Zend_Db_Adapter_Pdo_Abstract
     */
    protected $db;

    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
    {
        $this->db = Zend_Registry::get('db');
        parent::__construct ( $request, $response, $invokeArgs );
    }

    protected function getIdentity()
    {
        $auth = Zend_Auth::getInstance();
        return $auth->getIdentity();
    }

    protected function hasIdentity()
    {
        $auth = Zend_Auth::getInstance();
        return $auth->hasIdentity();
    }

    function preDispatch()
    {
        $auth = Zend_Auth::getInstance();

        $module = Zend_Controller_Front::getInstance()->getRequest()->getModuleName();
		$controller = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
		$resource = "{$module}_{$controller}";
        $acl = Zend_Registry::get ( 'acl' );
        $identity = is_null($auth->getIdentity()) ? 'guest' : $auth->getIdentity();
        if (!$acl->isAllowed($identity, $resource)) {
            if (!$auth->hasIdentity()) {
                $this->_redirect('auth/login');
            }
        }
    }

    public function jsAction()
    {
        $js = $this->_getParam ( 'file' );
        if (! is_null ( $js )) {
            $this->_helper->viewRenderer->setNoRender ();
            echo $this->_helper->getHelper ( 'FileContent' )->js ( $this, ( string ) $js );
        } else {
            throw new Exception ( "js namespace ({$js}) not found" );
        }
    }

    public function cssAction()
    {
        $css = $this->_getParam ( 'file' );
        if (! is_null ( $css )) {
            $this->_helper->viewRenderer->setNoRender ();
            echo $this->_helper->getHelper ( 'FileContent' )->css ( $this, ( string ) $css );
        } else {
            throw new Exception ( "css namespace ({$css}) not found" );
        }
    }
}
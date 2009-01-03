<?php
class AuthController extends Gam_Controller_Action
{
    public $security = array(
	   'guest'  => 'allow',
	   'member' => 'allow',
	   'admin'  => 'allow',
	   );
	
    function indexAction()
    {
        $this->_redirect('/');
    }
    
    function logintooltipAction()
    {
    }
    
    function dologinAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        try {
            Zend_Loader::loadClass('Zend_Filter_StripTags');
            $filter = new Zend_Filter_StripTags();
            $username = $this->getParam('user');
            $password = $this->getParam('password');
            $authAdapter = new Zend_Auth_Adapter_DbTable($this->db);
            $authAdapter
                ->setTableName('users')
                ->setIdentityColumn('username')
                ->setCredentialColumn('password');
            //$authAdapter = new Gam_Auth_Adapter($username, $password);
            
            $authAdapter
                ->setIdentity($username)
                ->setCredential($password);
                
            $auth = Zend_Auth::getInstance();
            $result = $auth->authenticate($authAdapter);
            if ($result->isValid()) {
                $auth->getStorage()->write($username);
                echo Zend_Json::encode(array('status' => 1, 'txt' => 'logged as '.$username));
            } else {
                echo Zend_Json::encode(array('status' => 0, 'txt' => 'login incorrect'));
            } 
        } catch (Exception $e) {
            echo Zend_Json::encode(array('status' => 0, 'txt' => 'Error:' ));
        }
    }
    
    function dologoffAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        try {
            $auth = Zend_Auth::getInstance();
            if ($auth->hasIdentity()) {
                Zend_Auth::getInstance()->clearIdentity();
                $out = 1;
                $txt = 'logged out';
            } else {
                $out = 0;
                $txt = 'Trying to log out without log in';
            }
        } catch (Exception $e) {
            $out = 0;
            $txt = 'unexpected error';
        }
        echo Zend_Json::encode(array('status' => $out, 'txt' => $txt));
    }
    
    
    
    
    
    
    
    
    
    
    function loginAction()
    {
        $this->view->message = '';
        if ($this->_request->isPost()) {
            // collect the data from the user
            Zend_Loader::loadClass('Zend_Filter_StripTags');
            $filter = new Zend_Filter_StripTags();
            $username = $filter->filter($this->_request->getPost('username'));
            $password = $filter->filter($this->_request->getPost('password'));
            
            if (empty($username)) {
                $this->view->message = 'Please provide a username.';
            } else {
                // setup Zend_Auth adapter for a database table
                
                $authAdapter = new Gam_Auth_Adapter($username, $password);
                
                // do the authentication 
                $auth = Zend_Auth::getInstance();
                $result = $auth->authenticate($authAdapter);
                if ($result->isValid()) {
                    // success : store database row to auth's storage system
                    // (not the password though!)
                    //$data = $authAdapter->getResultRowObject(null, 'password');
                    $auth->getStorage()->write($username);
                    $this->_redirect('/');
                } else {
                    // failure: clear database row from session
                    $this->view->message = 'Login failed.';
                }
            }
        }
    }
    
    function logoutAction()
    {
        try {
            $this->_helper->viewRenderer->setNoRender();
            Zend_Auth::getInstance()->clearIdentity();
            $out = 1;
        } catch (Exception $e) {
            $out = 0;
        }
        return Zend_Json::encode(array('status' => $out));
    }
}
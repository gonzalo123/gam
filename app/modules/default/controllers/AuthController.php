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
            echo Zend_Json::encode(array('status' => 0, 'txt' => $this->exceptionMsg($e, 'Error') ));
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
            $txt = $this->exceptionMsg($e, 'unexpected error');
        }
        echo Zend_Json::encode(array('status' => $out, 'txt' => $txt));
    }
}
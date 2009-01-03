<?php
class IndexController extends Gam_Controller_Action
{
	public $js = array(
        'init' => array(
            'dojoRequires.js',
            'dojoStores.js',
            //'onkeypress.js',
            'Main.js',
            'MenuActions.js',
            'Tabs.js',
            'Apps.js',
	   )
	);
	
	public $css = array(
        'init' => array(
            //'reset.css',
            'init.css',
            'toaster.css',
	   )
	);
	
	public function aboutnotesAction()
	{    
	}
	public function aboutplacesAction()
	{    
	}
	public function aboutbooksAction()
	{    
	}
	public function indexAction()
	{
	    $auth = Zend_Auth::getInstance();
	    $this->view->dojoStyle = Zend_Registry::get('config')->dojoStyle;
	    $this->view->logged = ($auth->hasIdentity()) ? 1 : 0;
        $this->view->pageTitle = Zend_Registry::get('config')->sitename;
        $this->view->js = $this->getClientUrl('js', 'init');
        $this->view->css = $this->getClientUrl('css', 'init');
	}
	
	public function sendmailAction()
	{
	    $this->setNoRender();
	    $subject = $this->_getParam( 'subject' );
	    $body    = $this->_getParam( 'body' );
	    
	    if (mail(Zend_Registry::get('config')->mymail, $subject, $body)) {
	        echo Zend_Json::encode(array('status' => 1, 'txt' => 'Mail sent'));
	    } else {
	        echo Zend_Json::encode(array('status' => 0, 'txt' => 'Error sending the email'));
	    }
	}
}

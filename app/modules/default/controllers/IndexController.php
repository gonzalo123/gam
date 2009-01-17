<?php
class IndexController extends Gam_Controller_Action
{
    public $js = array(
        'init' => array(
            'dojoRequires.js',
            'dojoStores.js',
            'Main.js',
            'MenuActions.js',
            'Tabs.js',
            'Apps.js',

            'dynamic' => array(
                'gamJs',
                ),

        )
    );

    public $css = array(
        'init' => array(
            //'reset.css',
            'init.css',
            'toaster.css',
        )
    );

    protected function renderJs($name, $options=array())
    {
        $view = new Zend_View();
        $module = $this->getRequest()->getModuleName();
		$controller = $this->getRequest()->getControllerName();
        $view->addScriptPath(GamBASEPATH . "/app/modules/{$module}/views/scripts/{$controller}/js/");

        if (count($options)>0) {
            foreach ($options as $key => $value) {
                $view->{$key} = $value;
            }
        }

        return $view->render($name);
    }
    public function gamJs()
    {
        echo $this->renderJs('gamJs.js', array(
            'extraFeeds' => array(
                'ZF' => 'http://devzone.zend.com/tag/Zend_Framework_Management/format/rss2.0',
                //'php' => 'http://www.php.net/feed.atom',
                //'ajaxian' => 'http://www.ajaxian.com/index.xml',
                'dojo' => 'http://dojotoolkit.org/blog/feed',
                )
            )
        );
    }

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

        $body    = $this->_getParam( 'body' );
        $email   = $this->_getParam( 'email' );
        $subject = "From: {$email}. ".$this->_getParam( 'subject' );

        if (mail(Zend_Registry::get('config')->mymail, $subject, $body)) {
            echo Zend_Json::encode(array('status' => 1, 'txt' => 'Mail sent'));
        } else {
            echo Zend_Json::encode(array('status' => 0, 'txt' => 'Error sending the email'));
        }
    }

    public function blogrssurlAction()
    {
        $this->setNoRender();
        echo Zend_Json::encode(array(array(
        'title' => 'My blog',
        'url'   => Zend_Registry::get('config')->myblog->url . Zend_Registry::get('config')->myblog->atom
        ),array(
        'title' => 'TMZ',
        'url' => 'http://www.tmz.com/rss.xml'
        )));
    }
}

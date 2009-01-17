<?php
interface Gam_Controller_Interface_Comet
{
    function cometAction();
}

class SlidesController extends Gam_Controller_Action
{
    public $js = array(
        'init' => array(
        'dojoRequires.js',
        'Cometd.js',
        'Slides.js',
        )
    );

    public $css = array(
        'init' => array(
        'init.css',
        )
    );

    public function indexAction()
    {
        $auth = Zend_Auth::getInstance();
        $this->view->dojoStyle = Zend_Registry::get('config')->dojoStyle;
        $this->view->logged = ($auth->hasIdentity()) ? 1 : 0;
        $this->view->pageTitle = Zend_Registry::get('config')->sitename;
        $this->view->js = $this->getClientUrl('js', 'init');
        $this->view->css = $this->getClientUrl('css', 'init');
    }

    public function cometAction()
    {
        $this->setNoRender();
        echo Zend_Json::encode(Gam_Comet::run($this->_getParam('key')));
    }
    /*
    public function getslidesAction()
    {
        $this->setNoRender();
        $dir = GamBASEPATH . "/db/slides/vf1";
        if (is_dir($dir)) {
            if ($handle = opendir($dir)) {
                while (false !== ($file = readdir($handle))) {
                    if ($file != "." && $file != "..") {
                        $out[] = array(
                            'id'    => $file,
                            //'thumb' => "/slides/img/id/" . urlencode($file) . "/key/{$this->_getParam('key')}",
                            'large' => "/slides/img/id/" . urlencode($file) . "/key/{$this->_getParam('key')}",
                            //'title' => $file,
                            //'link'  => null,
                        );
                    }
                }
                closedir($handle);
            }
        }
        echo new Zend_Dojo_Data('id', $out);
    }
    */

    public function getslidesAction()
    {
        $this->setNoRender();
        $dir = GamBASEPATH . "/db/slides/vf1";
        if (is_dir($dir)) {
            if ($handle = opendir($dir)) {
                while (false !== ($file = readdir($handle))) {
                    if ($file != "." && $file != "..") {
                        $out[] = "/slides/img/id/" . urlencode($file) . "/key/{$this->_getParam('key')}";
                    }
                }
                closedir($handle);
            }
        }
        echo Zend_Json::encode($out);
    }

    public function imgAction()
    {
        $this->setNoRender();
        $path = GamBASEPATH . "/db/slides/{$this->_getParam('key')}/{$this->_getParam('id')}";
        header('Content-Type: image/jpg');
        @readfile($path);
    }
}
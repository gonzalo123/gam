<?php
class PlacesController extends Gam_Controller_Action
{
	public $js = array(
        'init' => array(
            'dojoRequires.js',
            'dojoStores.js',
            'Places.js',
	   )
	);

	public $css = array(
        'init' => array(
            'init.css',
            'toaster.css',
	   )
	);

	public function indexAction()
	{
	}

	public function adddialogAction()
	{
	}

	public function editdialogAction()
	{
	    $sql = "
	    SELECT
	       id id,
	       title title,
	       body body
        FROM
            tblPlaces
        WHERE
            id = ?
        ";
	    $this->view->data = $this->_db->fetchRow($sql, array($this->getParam('id')), Zend_Db::FETCH_OBJ);
	    $a=1;
	}

	private function _placesList()
	{
	    $sql = "
	    SELECT
	       id id,
	       lat lat,
	       lng lng,
	       title title,
	       body body
        FROM
            tblPlaces
        ";
	    return $this->_db->fetchAll($sql, array(), Zend_Db::FETCH_OBJ);
	}

	public function editAction()
	{
	   $this->setNoRender();
	    if ($this->getIdentity() != Zend_Registry::get ('config')->owner) {
	        $txt = "Only Administrator can perform this action";
            $status = 9;
        } else {
    	    try {
        	    $this->_db->beginTransaction();
        	    $this->_db->query("
        	       UPDATE tblPlaces
        	       SET
        	           title = ?,
        	           body = ?
        	       WHERE
        	           id = ?",  array(
        	           $this->getParam('title'),
        	           $this->getParam('body'),
        	           $this->getParam('id')
        	           ));
        	    $this->_db->commit();
        	    $txt = "Place edit";
        	    $status = 1;
    	    } catch (Exception $e) {
    	        $status = 0;
                $txt = $this->exceptionMsg($e, "System Error");
    	    }
	    }
	    echo Zend_Json::encode(array('status' => $status, 'txt' => $txt));
	}

	public function saveAction()
	{
	   $this->setNoRender();
	    if ($this->getIdentity() != Zend_Registry::get ('config')->owner) {
	        $txt = "Only Administrator can perform this action";
            $status = 9;
        } else {
    	    try {
        	    $this->_db->beginTransaction();
        	    $id = (integer) $this->_db->fetchOne("SELECT max(id) FROM tblPlaces") + 1;
        	    $this->_db->query("
        	       INSERT INTO tblPlaces (id, lat, lng, title, body)
        	       VALUES (?, ?, ?, ?, ?) ",  array(
        	           $id,
        	           $this->getParam('lat'),
        	           $this->getParam('lng'),
        	           $this->getParam('title'),
        	           $this->getParam('body')
        	           ));
        	    $this->_db->commit();
        	    $txt = "Place added";
        	    $status = 1;
    	    } catch (Exception $e) {
    	        $status = 0;
                $txt = "System Error";
    	    }
	    }
	    echo Zend_Json::encode(array('status' => $status, 'id' => $id, 'txt' => $txt));
	}

	public function deleteAction()
	{
	   $this->setNoRender();
	    if ($this->getIdentity() != Zend_Registry::get ('config')->owner) {
	        $txt = "Only Administrator can perform this action";
            $status = 9;
        } else {
    	    try {
        	    $this->_db->beginTransaction();
        	    $this->_db->query("DELETE FROM tblPlaces WHERE id = ?",  array($this->getParam('id')));
        	    $this->_db->commit();
        	    $txt = "Place deleted";
        	    $status = 1;
    	    } catch (Exception $e) {
    	        $status = 0;
                $txt = $this->exceptionMsg($e, "System Error");
    	    }
	    }
	    echo Zend_Json::encode(array('status' => $status, 'txt' => $txt));
	}

	public function getplacesAction()
	{
	    $this->setNoRender();
	    echo Zend_Json::encode($this->_placesList());
	}

	public function listAction()
	{
	    $this->setNoRender();
	    echo new Zend_Dojo_Data('id', $this->_placesList());
	}
}
<?php
class NotesController extends Gam_Controller_Action
{
	public $js = array(
        'init' => array(
            'dojoRequires.js',
            'Notes.js',
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

	public function addnotedialogAction()
	{
	}

	/**
	 * Gets Lucene Index
	 *
	 * @return Zend_Search_Lucene_Interface
	 */
	private function _getLuceneIndex() {
	    $path = GamBASEPATH . Zend_Registry::get('config')->lucene->path;
	    if (is_dir($path)) {
	        return Zend_Search_Lucene::open($path);
	    } else {
            return Zend_Search_Lucene::create($path);
	    }
	}

	private function _addLuceneDoc($id, $title, $body)
	{
	    $index = $this->_getLuceneIndex();
        $doc = new Zend_Search_Lucene_Document();
        $doc->addField(Zend_Search_Lucene_Field::Text('pk', $id));
        $doc->addField(Zend_Search_Lucene_Field::Text('title', $title));
        $doc->addField(Zend_Search_Lucene_Field::Text('body', $body));
        $index->addDocument($doc);
	}

	public function addnoteAction()
	{
	    $this->setNoRender();
	    if (!$this->hasIdentity()) {
	        $txt = "Only logged users can perform this action";
            $status = 9;
        } else {
            try {
                $this->db->beginTransaction();

                $pk = (integer) $this->db->fetchOne("SELECT max(id) FROM tblNotes") + 1;
        	    $this->db->query("
        	       INSERT INTO tblNotes (id, title, body)
        	       VALUES (?, ?, ?) ",  array(
        	           $pk,
        	           $this->getParam('title'),
        	           $this->getParam('body'),
        	           ));
                //$this->_addLuceneDoc($pk, $this->getParam('title'), $this->getParam('body'));

                $this->db->commit();
                $status = 1;
        	    $txt = 'Note saved';
            } catch (Exception $e) {
                $status = 0;
        	    $txt = $this->exceptionMsg($e, 'System error');
            }
        }
        echo Zend_Json::encode(array('status' => $status, 'txt' => $txt));
	}

	public function noteAction()
	{
        $data = $this->db->fetchRow("
	       SELECT
	           id id,
	           title title,
	           body body
	       FROM
	           tblNotes
	       WHERE
	           id = ?", array($this->getParam('id')), Zend_Db::FETCH_OBJ);
        $this->view->id    = $data->id;
        $this->view->title = $data->title;
        $this->view->body  = $data->body;
	}

	public function storegrdAction()
	{
	    $this->setNoRender();
	    /*
	    $index = $this->_getLuceneIndex();
	    $hints = $index->find($this->getParam('key'));
	    $out = array();
	    foreach ($hints as $hint) {
	        $out[] = array(
                'id'    => $hint->id,
                'pk'    => $hint->pk,
                'score' => $hint->score,
                'title' => $hint->title,
                'body'  => $hint->body,
	        );
	    }
	    */
	    $sql = "
	    SELECT
	       id id,
	       title title,
	       body body
        FROM
            tblNotes
        WHERE
            body like '%{$this->getParam('key')}%' OR
            title like '%{$this->getParam('key')}%'";
	    echo new Zend_Dojo_Data('id', $this->db->fetchAll($sql), array(), Zend_Db::FETCH_OBJ);
	}

	private function _luceneDelete($id)
	{
	    $index = $this->_getLuceneIndex();
	    $index->delete($id);
	}

	public function doeditAction()
	{
	    $this->setNoRender();
	    if (!$this->hasIdentity()) {
	        $txt = "Only logged users can perform this action";
            $status = 9;
        } else {
            try {
        	    $this->db->beginTransaction();

        	    //$this->_luceneDelete($this->getParam('id'));
        	    //$this->_addLuceneDoc($this->getParam('pk'), $this->getParam('title'), $this->getParam('body'));

        	    $this->db->query("
        	       UPDATE tblNotes
        	       SET
        	           title = ?,
        	           body = ?
        	       WHERE id = ?", array(
                       $this->getParam('title'),
                       $this->getParam('body'),
                       $this->getParam('id')
        	           ));
        	    $this->db->commit();
        	    $status = 1;
        	    $txt = 'Note updated';
            } catch (Exception $e) {
                $status = 0;
        	    $txt = $this->exceptionMsg($e, 'txt');
            }
        }
        echo Zend_Json::encode(array('status' => $status, 'txt' => $txt));
	}

	public function dodeleteAction()
	{
	    $this->setNoRender();
	    if (!$this->hasIdentity()) {
	        $txt = "Only logged users can perform this action";
            $status = 9;
        } else {
            try {
    	       $this->db->beginTransaction();
    	       //$this->_luceneDelete($this->getParam('id'));
    	       $this->db->query("
    	           DELETE FROM tblNotes
    	           WHERE id = ?", array(
                    $this->getParam('id')
    	               ));
    	       $this->db->commit();
    	       $status = 1;
        	   $txt = 'Note deleted';
            } catch (Exception $e) {
                $status = 0;
                $txt = $this->exceptionMsg($e, 'System error');
            }
        }
        echo Zend_Json::encode(array('status' => $status, 'txt' => $txt));
	}
}
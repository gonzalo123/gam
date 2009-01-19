<?php
class BooksController extends Gam_Controller_Action
{
	public $js = array(
        'init' => array(
            'dojoRequires.js',
            'dojoStores.js',
            'Books.js',
            'dynamic' => array(
                'gamJs',
                ),
	   )
	);

	public $css = array(
        'init' => array(
            'init.css',
            'toaster.css',
	   )
	);

	public function gamJs()
	{
	    //echo "alert('1');";
	}

	public function indexAction()
	{
	}

	public function adddialogAction()
	{
	}

	public function searchAction()
	{
	    $this->setNoRender();
        echo new Zend_Dojo_Data('id', $out);
	}

	public function editbookAction()
	{
	    $this->view->data = $this->_getBookInfo($this->getParam('id'));
	    $this->view->readChecked = $this->view->readingChecked = $this->view->toreadChecked = null;
	    switch ($this->view->data->status) {
	        case 0:
	            $this->view->data->readChecked = "checked";
	            break;
	        case 1:
	            $this->view->data->readingChecked = "checked";
	            break;
            case 2:
                $this->view->data->toReadChecked = "checked";
	            break;
	    }
	}

	private function _getBookInfo($id)
	{
	    return $this->_db->fetchRow("
	       SELECT
	           id id,
	           title title,
	           author author,
	           bookyear bookyear,
	           readdate readdate,
	           why why,
	           status status,
	           isbn isbn
	       FROM
	           tblBooks
	       WHERE
	           id = ?", array($id), Zend_Db::FETCH_OBJ);
	}

	public function bookeditAction()
	{
	    $this->view->data = $this->__getBookInfo($this->getParam('id'));
	    //list($this->view->imageUrl, $this->view->imageWidth, $this->view->imageHeight, $this->view->content) = $this->_tryCoverFromAmazon($this->view->data);
	}

	public function bookAction()
	{
	    $this->view->data = $this->_getBookInfo($this->getParam('id'));
	    //list($this->view->imageUrl, $this->view->imageWidth, $this->view->imageHeight, $this->view->content) = $this->_tryCoverFromAmazon($this->view->data);
	}

	private function _tryCoverFromAmazon($data)
	{
	    try {
            $amazon = new Zend_Service_Amazon(Zend_Registry::get('config')->amazonkey);
            $amazonSearch = array();
            $amazonSearch['SearchIndex'] = 'Books';
            $amazonSearch['ResponseGroup'] = 'Medium';

            if ($data->title) {
                $amazonSearch['Title'] = $data->title;
            }
            if ($data->author) {
                $amazonSearch['Author'] = $data->author;
            }

            if (count($amazonSearch) > 2) {
                $results = $amazon->itemSearch($amazonSearch);

                $arr = array();
                foreach ($results as $result) {
                    $arr[] = $result;
                }
                $content = null;
                if (isset($arr[0]->EditorialReviews) && $arr[0]->EditorialReviews[0] instanceof Zend_Service_Amazon_EditorialReview) {
                    $content = $arr[0]->EditorialReviews[0]->Content;
                }

                if ($arr[0]->MediumImage->Url instanceof Zend_Uri) {
                    return array(
                        $arr[0]->MediumImage->Url->getUri(),
                        $arr[0]->MediumImage->amazon->Width,
                        $arr[0]->MediumImage->amazon->Height,
                        $content);
                } else {
                    return array(null, null, null, $content);
                }
            }
	    } catch (Exception $e) {
	        die($e->getMessage());
	    }
	    return array(null, null, null);
	}

	public function doeditAction()
	{
	    $this->setNoRender();
	    if ($this->getIdentity() != Zend_Registry::get ('config')->owner) {
	        $txt = "Only Administrator can perform this action";
            $status = 9;
        } else {
    	    try {
    	        $this->_db->beginTransaction();
    	        $this->_db->query("
        	       UPDATE tblBooks
        	       SET
        	           isbn = ?,
        	           title = ?,
        	           author = ?,
        	           bookyear = ?,
        	           why = ?,
        	           status = ?
        	       WHERE id = ?", array(
    	               $this->getParam('isbn'),
    	               $this->getParam('title'),
    	               $this->getParam('author'),
    	               $this->getParam('bookyear'),
    	               $this->getParam('why'),
    	               $this->getParam('status'),
    	               $this->getParam('id'),
        	           ));

    	        $this->_db->commit();
    	        $status = 1;
    	        $txt = "Info updated";
            } catch (Exception $e) {
    	        $status = 0;
                $txt = $this->exceptionMsg($e, "System Error");
    	    }
        }
	    echo Zend_Json::encode(array('status' => $status, 'txt' => $txt));
	}

	public function dodeleteAction()
	{
	    $this->setNoRender();
	    if ($this->getIdentity() != Zend_Registry::get ('config')->owner) {
	        $txt = "Only Administrator can perform this action";
            $status = 9;
        } else {
    	    try {
    	        $this->_db->beginTransaction();
    	        $this->_db->query("
        	       DELETE FROM tblBooks
        	       WHERE id = ?", array(
    	               $this->getParam('id'),
        	           ));
    	        $this->_db->commit();
    	        $status = 1;
    	        $txt = "Book deleted";
            } catch (Exception $e) {
    	        $status = 0;
                $txt = $this->exceptionMsg($e, "System Error");
    	    }
        }
	    echo Zend_Json::encode(array('status' => $status, 'txt' => $txt));
	}

	public function markasAction()
	{
	    $this->setNoRender();
	    if ($this->getIdentity() != Zend_Registry::get ('config')->owner) {
	        $txt = "Only Administrator can perform this action";
            $status = 9;
        } else {
    	    try {
    	        $this->_db->beginTransaction();
    	        $this->_db->query("
        	       UPDATE tblBooks SET status=? WHERE id = ? ",  array(
        	           $this->getParam('status'),
        	           $this->getParam('id')
        	           ));
    	        $this->_db->commit();
    	        $status = 1;
    	        $txt = 'Info updated succesfully';
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
        	    $id = (integer) $this->_db->fetchOne("SELECT max(id) FROM tblBooks") + 1;
        	    $this->_db->query("
        	       INSERT INTO tblBooks (id, isbn, title, author, bookyear, why, status)
        	       VALUES (?, ?, ?, ?, ?, ?, ?) ",  array(
        	           $id,
        	           $this->getParam('isbn'),
        	           $this->getParam('title'),
        	           $this->getParam('author'),
        	           $this->getParam('bookyear'),
        	           $this->getParam('why'),
        	           $this->getParam('status'),
        	           ));
        	    $this->_db->commit();
        	    $txt = "Book added";
        	    $status = 1;
    	    } catch (Exception $e) {
    	        $status = 0;
                $txt = $this->exceptionMsg($e, "System Error");
    	    }
	    }
	    echo Zend_Json::encode(array('status' => $status, 'id' => $id, 'txt' => $txt));
	}

	public function listAction()
	{
	    $this->setNoRender();
	    $sql = "
	    SELECT
	       id id,
	       title title,
	       author author,
	       bookyear bookyear,
	       why why
        FROM
            tblBooks
        WHERE
            status = ?";
	    echo new Zend_Dojo_Data('id', $this->_db->fetchAll($sql, array($this->getParam('type'))));
	}
}
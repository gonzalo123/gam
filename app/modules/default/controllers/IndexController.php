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

        $body    = $this->_getParam( 'body' );
        $email   = $this->_getParam( 'email' );
        $subject = "From: {$email}. ".$this->_getParam( 'subject' );

        if (mail(Zend_Registry::get('config')->mymail, $subject, $body)) {
            echo Zend_Json::encode(array('status' => 1, 'txt' => 'Mail sent'));
        } else {
            echo Zend_Json::encode(array('status' => 0, 'txt' => 'Error sending the email'));
        }
    }
    /*
    public function blogrssAction()
    {
    $url = "http://gonzaloayuso.blogspot.com/feeds/posts/default";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_FAILONERROR, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 3);
    curl_setopt($ch, CURLOPT_GET, 1);
    //curl_setopt($ch, CURLOPT_GETFIELDS, "alt=rss");
    $result = curl_exec($ch);
    curl_close($ch);
    //header('Content-Type: application/xhtml+xml; charset=utf-8');
    echo $result;
    }
    */

    private function _normaliseDate($date)
    {
        $date =  preg_replace("/([0-9])T([0-9])/", "$1 $2", $date);
        $date =  preg_replace("/([\+\-][0-9]{2}):([0-9]{2})/", "$1$2", $date);
        $time = strtotime($date);
        if (($time - time()) > 3600) {
            $time = time();
        }
        $date = gmdate("Y-m-d H:i:s O", $time);
        return $date;
    }
    private function _dirtyParseEntry(Zend_Feed_Entry_Abstract $item)
    {
        // for those times when Zend_Feed lets us down...
        $dom = $item->getDOM();

        // get a unique id identifying this entry online
        $guid = '';
        if ($item->guid()) {
            $guid = $item->guid();
        } elseif ($item->id()) {
            $guid = $item->id();
        } else {
            $guid = $item->link();
        }

        // fetch a title
        $title = '';
        $title = $item->title();

        // get a description or similar
        $description = '';
        if ($item->description()) {
            $description = $item->description;
        } else {
            $description = $title;
        }

        // normalise content
        $contentOriginal = '';
        $content = '';
        if ($item->encoded()) {
            $contentOriginal =
            html_entity_decode($item->encoded(), ENT_QUOTES, 'UTF-8');
        } elseif ($item->content()) {
            $contentOriginal = $item->content();
        }
        // Purify and normalise content to XHTML 1.0 Transitional
        //$purifier = new HTMLPurifier();
        //$content = $purifier->purify($contentOriginal);
        $content = $contentOriginal;

        // fetch entry item link (adjust if href holds it)
        $link = '';
        if($item->link()) {
            $link = $item->link();
        } else {
            $links = $dom->getElementsByTagName('link');
            $link = $links->item(0)->getAttribute('href');
        }

        // get the author name
        $author = '';
        $creators = $dom->getElementsByTagNameNS(
            'http://purl.org/dc/elements/1.1/',
            'creator'
        );
        $creator = $creators->item(0)->nodeValue;
        if($creator) {
            $author = $creator;
        } elseif($item->author() && is_string($item->author())) {
            $author = $item->author();
        } else {
            $author = $item->author->name();
        }

        // get a publication date and normalise
        $date = '';
        $dcdates = $dom->getElementsByTagNameNS(
        'http://purl.org/dc/elements/1.1/',
        'date'
        );
        $dcdate = $dcdates->item(0)->nodeValue;
        if($dcdate) {
            $date = $dcdate;
        } elseif ($item->pubDate()) {
            $date = $item->pubDate();
        } elseif ($item->published()) {
            $date = $item->published();
        } elseif ($item->created()) {
            $date = $item->created();
        } elseif ($item->updated()) {
            $date = $item->updated();
        } elseif ($item->modified()) {
            $date = $item->modified();
        }
        $date = $this->_normaliseDate($date);

        // get a unique content hash to detect future content changes
        $hash = '';
        $arrayContent = array($title, $contentOriginal, $link);
        $stringContent = implode(' ', $arrayContent);
        $hash = md5($stringContent);

        // put together result object
        $result = new stdClass;
        $arr = explode('-', $date);
        $result->dirtyDate = $arr[0] . '/' . $arr[1] . '/' .  $arr[1];
        $result->dirtyUrl = $arr[0] . '/' . $arr[1] . '/' . str_replace(' ', '-', strtolower($title)) . '.html';
        $result->guid = $guid;
        $result->title = $title;
        $result->url = $link;
        $result->description = $description;
        $result->date = $date;
        $result->creator = $author;
        $result->content = $content;
        $result->hash = $hash;
        return $result;
    }
    public function blogrssAction()
    {
        try {
            $rss = Zend_Feed::import(Zend_Registry::get('config')->myblog->url . Zend_Registry::get('config')->myblog->atom);
        } catch (Exception $e) {
            $this->setNoRender();
            echo $this->exceptionMsg($e, "Error Fetching the feed");
            exit;
        }
        // Initialize the channel data array


        $channel = array(
        'title'       => $rss->title(),
        'link'        => $rss->link(),
        'description' => $rss->description(),
        'items'       => array()
        );

        foreach ($rss as $item) {

            $parsedEntry = $this->_dirtyParseEntry($item);

            $channel['items'][] = array(
                'date'        => $parsedEntry->dirtyDate,
                'title'       => $item->title(),
                'link'        => Zend_Registry::get('config')->myblog->url . $parsedEntry->dirtyUrl,
                'description' => $item->description()
            );
        }
        $this->myblog = Zend_Registry::get('config')->myblog->url;
        $this->view->channel = $channel;
    }
}

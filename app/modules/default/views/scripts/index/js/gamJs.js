var gamJs = {
    feeds: [{
        title: "My blog",
        url: "<?= Zend_Registry::get('config')->myblog->atom ?>"
    }, {
        title: "source",
        url: "<?= Zend_Registry::get('config')->github ?>"
    }
<?php foreach ($this->extraFeeds as $key => $value)
    echo ",{title: '{$key}', url: '{$value}'}";
?>
    ]
}
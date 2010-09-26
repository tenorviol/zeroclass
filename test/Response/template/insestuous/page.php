<?php

// set the header and footer templates
$page->header = '/header';
$page->footer = '/footer';

// adjust some other public properties
$page->title = 'MyPage';
$page->copyright = 'Copyright &copy; 2010';

// changes to any object in the vars array will be visible to all templates
$css->files[] = '/incestuous.css';

// changes to other data types will not

?>
MyPage content...

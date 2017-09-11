<?php
$menus = array(
    'index' => array( 'id' => 'home', 'title' => 'HOME', ),
    'about-us' => array( 'id' => 'about-us', 'title' => 'ABOUT US', ),
    'services' => array( 'id' => 'services', 'title' => 'SERVICES', ),
    'blog' => array( 'id' => 'blog-page', 'title' => 'BLOG', ),
    'contact' => array( 'id' => 'contact-us', 'title' => 'SIGN UP', )
);

echo generate_site_menu_html($menus,false);
?>
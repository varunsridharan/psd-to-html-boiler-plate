<?php
define("ABS_PATH",__DIR__.'/');
include("parts/bulk-images.php");
include("parts/texts.php"); 

function the_title($args = array()){
    echo get_the_title($args);
}

function _placeholder($data = array()){
   echo placeholder($data);
}

function _lang($key){
    echo lang($key);
}

function _ul($list, $attributes = '') {
    echo ul($list, $attributes);
}

function _ol($list, $attributes = '') {
    echo ol($list, $attributes);
}

function _img_url($src,$placeholder = false){
    echo img_url($src,$placeholder);
}

function _eimg($src,$class = '',$attributes = '',$placeholder = false){
    echo img($src,$class,$attributes,$placeholder);
}

function _icon($id){
    echo icon($id);
}

function _fa($id){ echo fa($id); }

function _dripicons($id){ echo dripicons($id); }

function _flaticon($id){ echo flaticon($id); }

function _br($count = 1) {
    echo str_repeat('<br />', $count);
}

function _nbs($num = 1) {
    echo str_repeat('&nbsp;', $num);
}

function current_page(){
    if(defined("GENERATE_HTML")){
        global $current_file;
        $active_pages = str_replace(array(".html",'.php'),array('',''),basename($current_file));
    } else {
        $active_pages = str_replace(array(".html",'.php'),array('',''),get_active_page());
    }
    
    return $active_pages;
}

/* =================================== */ 
function generate_site_menu_html($menus,$is_child = false){
    $html_menu = '';
    
    
    if(!$is_child){
        $html_menu = '<ul class="nav navbar-nav navbar-right underline">';    
    } else {
        $html_menu = '<ul class="dropdown-menu">';
    }
    
    
    foreach($menus as $slug => $menu){
        $ext = defined("GENERATE_HTML") ? '.html' : '.php';
        $link = file_exists(ABS_PATH.$slug.'.php') ? $slug.$ext : '#';
        $id = isset($menu['id']) ? $menu['id'] : '#';
        $name = isset($menu['title']) ? $menu['title'] : '';
        $title = isset($menu['title']) ? $menu['title'] : '';
        $has_child = false;
        $li_class = '';
        if(defined("GENERATE_HTML")){
            global $current_file;
            $active_pages = str_replace(array(".html",'.php'),array('',''),basename($current_file));
        } else {
            $active_pages = str_replace(array(".html",'.php'),array('',''),get_active_page());
        }
        
        
        $childs = '';
        
        
        if($active_pages == $slug){ 
            $li_class .= ' active '; 
        } else {
            global $current_active_page,$current_active_page_link;
            if($current_active_page == $slug){ 
                $li_class .= ' active '; 
            
                if(!empty($current_active_page_link)){
                    $l = str_replace(array(".html",'.php'),array('',''),basename($current_active_page_link));
                    $link = file_exists(ABS_PATH.$l.'.php') ? $l.$ext : "#";
                }
            }
        }
        
        if(isset($menu['childs'])){
            if(!empty($menu['childs'])){
                $has_child = true;
                $li_class .= ' dropdown ';
                $name .= ' <span class="caret"></span>';
                $childs = generate_site_menu_html($menu['childs'],true);
                
                if(isset($menu['childs'][$active_pages])){
                    $li_class .= ' active ';
                }
            }
        }
        
        
        
        $a_attrs = array( 'href' => $link, 'title' => $title, 'id' => $id, );        
        if(isset($menu['a_attrs'])){ $a_attrs = array_merge($menu['a_attrs'],$a_attrs); }
        $a_attrs = _stringify_attributes($a_attrs);
        
        
        $li_attrs = array( 'class' => $li_class, );        
        if(isset($menu['li_attrs'])){ $li_attrs = array_merge($menu['li_attrs'],$li_attrs); }        
        $li_attrs = _stringify_attributes($li_attrs);
        
        
        
        
        unset($menu['title']);
        
        $html_menu .= '<li '.$li_attrs.'> <a '.$a_attrs.'>'.$name.'</a>'.$childs.'</li>';
        
    }
    
    $html_menu .= '</ul>';  
    return $html_menu;
}

function assets_url($url,$force = false){
    if(defined("GENERATE_HTML")){
        return $url;
    } else {
        return 'assets/'.$url;
    }
}

function get_active_page(){
    $SCRIPT_FILENAME = $_SERVER['SCRIPT_FILENAME'];
    return basename($SCRIPT_FILENAME);
} 

function img_url($src = '',$placeholder = false){
    global $bulk_images;
    $is_placeholder = $placeholder;
    
    if(defined("GENERATE_HTML_DEMO")){
        $is_placeholder = true;
    }
    
    
    if(isset($bulk_images[$src])){
        $img_url = $bulk_images[$src]['src'];
        
        if(!isset($bulk_images[$src]['w'])){
            list($width, $height, $type, $attr) = getimagesize('assets/'.$img_url);
            $bulk_images[$src]['w'] = $width;
        }
        
        if(!isset($bulk_images[$src]['h'])){
            list($width, $height, $type, $attr) = getimagesize('assets/'.$img_url);
            $bulk_images[$src]['h'] = $height;
            
        }
        
        $img_url = assets_url($img_url);
        
        
        if($is_placeholder === false){
            return $img_url;    
        }
        
        $allowed_placeholder = isset($bulk_images[$src]['noplace']) ? false : true;
        
        if($allowed_placeholder){
            if(isset($bulk_images[$src]['placeholder'])){
                return $bulk_images[$src]['placeholder'];
            } else {
                return placeholder(array(
                    'w' => $bulk_images[$src]['w'],
                    'h' => $bulk_images[$src]['h']
                ));
            }  
        } else {
            return $img_url;
        }
    }
    
    return $src;
}

function icon($id = ''){ return "<i class='".$id."' ></i>"; }

function ul($list, $attributes = '') {
    return _list('ul', $list, $attributes);
}

function ol($list, $attributes = '') {
    return _list('ol', $list, $attributes);
}

function lang($key){
    global $langs;
    if(isset($langs[$key])){
        return $langs[$key];
    }
    
    return false;
}

function fa($id){
    $id = 'fa fa-'.$id;
    return icon($id);
}

function dripicons($id){
    $id = ' dripicons-'.$id;
    return icon($id);
}

function flaticon($id){
    $id = 'flaticon flaticon-'.$id;
    return icon($id);
}

function img($src = '',$class = '', $attributes = '', $placeholder = false ) {
    if ( ! is_array($src) ) { $src = array('src' => $src);}
    if ( ! isset($attributes['alt'])) { $attributes['alt'] = ''; }
    
    if( ! isset($attributes['class'])){$attributes['class'] = $class;}
    
    $img = '<img';

    foreach ($src as $k => $v) {
        if ($k === 'src') {
            $img .= ' src="'.img_url($v,$placeholder).'"';
        } else {
            $img .= ' '.$k.'="'.$v.'"';
        }
    }

    return $img._stringify_attributes($attributes).' />';
}

function parse_args($args, $defaults = '',$is_marr = false) {
    if (is_object ( $args ))
        $r = get_object_vars ( $args );
    elseif (is_array ( $args ))
        $r = & $args;
    else
        _parse_str ( $args, $r );

    if (is_array ( $defaults )){
        if($is_marr){
            return array_merge_recursive ($defaults,$r);
        }
        return array_merge ( $defaults, $r );
    }
    return $r;
} 

function _parse_str($string, &$array) {
    parse_str ( $string, $array );
    if (get_magic_quotes_gpc ())
        $array = stripslashes_deep ( $array );
    return $array;
} 

function get_template_part( $slug, $name = null ) {
	$templates = array();
	$name = (string) $name;
	if ( '' !== $name )
		$templates[] = "{$slug}-{$name}.php";

	$templates[] = "{$slug}.php";

	locate_template($templates, true, false);
}

function locate_template($template_names, $load = false, $require_once = true ) {
	$located = '';
	foreach ( (array) $template_names as $template_name ) {
		if ( !$template_name )
			continue;
		if ( file_exists(__DIR__ . '/template-parts/' . $template_name)) {
			$located = __DIR__ . '/template-parts/' . $template_name;
			break;
		}
	}
    
	if ( $load && '' != $located )
		load_template( $located, $require_once );

	return $located;
}

function load_template( $_template_file, $require_once = true ) {
	 
	if ( $require_once ) {
		require_once( $_template_file );
	} else {
		require( $_template_file );
	}
}

function placeholder($data = array()){
    $orginal = array(
        'w' => null,
        'h' => null,
        'text' => '',
        'color' => null,
    );
    
    
    $data = array_merge($orginal,$data);
    
    $query = 'http://via.placeholder.com/';
    if(!empty($data['w'])){
        $query .= $data['w'];
    }
    
    if(!empty($data['w']) && !empty($data['h'])){
        $query .= 'x';
    }
    
    if(!empty($data['h'])){
        $query .= $data['h'];
    }
    
    $query .= '/';
    $query .= isset($data['color']) ? $data['color'] : '';
    
    if(!empty($data['text'])){
        $query .= '?text='.urlencode($data['text']);
    }
    
    
    return $query;
}

function _list($type = 'ul', $list = array(), $attributes = '', $depth = 0) {
    if ( ! is_array($list)) {
        return $list;
    }

    $out = str_repeat(' ', $depth)
        // Write the opening list tag
        .'<'.$type._stringify_attributes($attributes).">\n";

    static $_last_list_item = '';
    foreach ($list as $key => $val) {
        $_last_list_item = $key;

        $out .= str_repeat(' ', $depth + 2).'<li>';

        if ( ! is_array($val)) {
            $out .= $val;
        } else {
            $out .= $_last_list_item."\n"._list($type, $val, '', $depth + 4).str_repeat(' ', $depth + 2);
        }

        $out .= "</li>\n";
    }

    return $out.str_repeat(' ', $depth).'</'.$type.">\n";
}

function link_tag($href = '', $rel = 'stylesheet', $type = 'text/css', $title = '', $media = '') {
    $link = '<link ';

    if (is_array($href)) {
        foreach ($href as $k => $v) {
            $link .= $k.'="'.$v.'" ';
        }
    } else {
        $link .= 'href="'.$href.'" ';
        $link .= 'rel="'.$rel.'" type="'.$type.'" ';
        if ($media !== '') { $link .= 'media="'.$media.'" '; }
        if ($title !== '') { $link .= 'title="'.$title.'" '; }
    }

    return $link."/>\n";
}

function script_tag($href = '', $type = 'text/javascript') {
    $link = '<script ';

    if (is_array($href)) {
        foreach ($href as $k => $v) { $link .= $k.'="'.$v.'" '; }
    } else {
        $link .= 'href="'.$href.'" ';
        $link .= 'rel="'.$rel.'" type="'.$type.'" ';
    }

    return $link."></script>\n";
}

function _stringify_attributes($attributes, $js = FALSE) {
    $atts = NULL;

    if (empty($attributes)) {
        return $atts;
    }

    if (is_string($attributes)) {
        return ' '.$attributes;
    }

    $attributes = (array) $attributes;

    foreach ($attributes as $key => $val) {
        $atts .= ($js) ? $key.'='.$val.',' : ' '.$key.'="'.$val.'"';
    }

    return rtrim($atts, ',');
}

function stringify_attributes($attributes) {
    if (empty($attributes)) { return ''; }

    if (is_object($attributes)) { $attributes = (array) $attributes; }

    if (is_array($attributes)) {
        $atts = '';
        foreach ($attributes as $key => $val) {
            if(empty($val)){continue;}
            $atts .= ' '.$key.'="'.$val.'"'; }
        return $atts;
    }

    if (is_string($attributes)) { return ' '.$attributes; }

    return FALSE;
}

function get_the_title($args = array()){
    $default = array(
        'before' =>'',
        'after' => '',
        'sep' => '|',
    );
    $args =  parse_args($args,$default);
    
    $page = current_page();
    $page = str_replace('-',' ',$page);
    $page = ucwords($page);
    $return = '';
    if(!empty($args['before'])){
        $return .= $args['before'].' '.$args['sep'];
    }
    
    $return .= $page;
    return $return;
}
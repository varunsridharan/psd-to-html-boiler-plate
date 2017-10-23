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

function _eimg($src,$class = '',$attributes = array(),$placeholder = false){
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
        $html_menu = '<ul class="nav navbar-nav underline">';    
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

function img($src = '',$class = '', $attributes = array(), $placeholder = false ) {
    if ( ! is_array($src) ) { $src = array('src' => $src);}
    if ( ! isset($attributes['alt'])) { 
        $attributes['alt'] = ''; 
    }
    
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

function get_template_part( $slug, $args = array(),$name = null ,$return = false) {
	$templates = array();
	$name = (string) $name;
	if ( '' !== $name )
		$templates[] = "{$slug}-{$name}.php";

	$templates[] = "{$slug}.php";

    $path = locate_template($templates, false, false);
    ob_start( );
	   load_template($path,$args,false);
    $output = ob_get_clean( );
    if($return){
        return $output;    
    }
    echo $output;
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

function load_template( $_template_file,$args = array(), $require_once = true ) {
    extract($args);
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






if (! function_exists ( 'generate_tabs' )) {
    function generate_tabs($tabs) {
        $defaults = array (
            'ul_id' => '',
            'ul_class' => 'nav nav-tabs ',
            'active_tab' => 'auto',
            'active_li_class' => 'active',
            'active_a_class' => '',
            'li_class' => '',
            'a_class' => '',
            'tab_content_class' => '',
            'menu_div_class' => '',
            'tabs' => array(),
            'contents' => array(),
        );
        
        $args = array_merge($defaults,$tabs);
        $is_only_menu = true;
        if(!empty($args['contents'])){$is_only_menu = false;}
        
        $tab_args = array (
            'id' => '',
            'slug' => '',
            'title' => '',
            'link' => '',
            'icon' => '',
            'icon_pos' => 'before' 
        );
        
        $menu_html = '<ul id="' . $args ['ul_id'] . '" class="' . $args ['ul_class'] . '" >';
        $content_html = '';
        $args['contents'] = array_filter($args['contents']);
        
        if($args['active_tab'] == 'auto'){
            $remove_data = array_diff(array_keys($args['tabs']),array_keys($args['contents']));
            foreach($remove_data as $name)
                unset($args['tabs'][$name]);
            
            $args['active_tab'] = current(array_keys($args['tabs']));
        } 
        
        
        
        
        foreach ( $args ['tabs'] as $slug => $arg ) {
            $tab_arr = array_merge ($tab_args, $arg );
            $li_class = $args ['li_class'];
            $a_class = $args ['a_class'];
            $name = $tab_arr ['title'];
            $final_text = $name;
            $link = '#' . $slug;
            $ex_arr = ' data-toggle="tab" ';
            
            if ($args ['active_tab'] == $slug) {
                $li_class .= $args ['active_li_class'];
                $a_class .= $args ['active_a_class'];
            }
            
            if (! empty ( $tab_arr ['icon'] )) {
                $icon = icon($tab_arr['icon']);
                if ($tab_arr ['icon_pos'] == 'after') {
                    $final_text = $name . ' ' . $icon;
                } else {
                    $final_text = $icon . ' ' . $name;
                }
            }
            
            if (! empty ( $tab_arr ['link'] )) { 
                $link = $tab_arr ['link']; 
                $ex_arr = '';
            }
            
            $menu_html .= '<li  class="' . $li_class . '">';
            
            $menu_html .= '<a  href="' . $link . '" ' . $ex_arr . '>' . $final_text . '</a>';
            $menu_html .= '</li>';
            
            if(!$is_only_menu){
                if(isset($args['contents'][$slug])){
                    $is_active_content = ($args['active_tab'] == $slug) ? 'active' : '';
                    $content_html .= '<div class="tab-pane '.$is_active_content.'" id="'.$slug.'">'.$args['contents'][$slug].'</div>';
                }
            }
        }
        
        $menu_html .= '</ul>';
        
        if(!$is_only_menu){
            $html = '<div class="'.$args['menu_div_class'].'">'.$menu_html.'<div class="tab-content '.$args['tab_content_class'].'">'.$content_html.'</div></div>';
        } else {
            $html = $menu_html;
        }
        
        
        return $html;
    }
} 
 

class LoremIpsum {
    private $first = true;
    public $words = array( 'lorem','ipsum',    'dolor','sit','amet','consectetur','adipiscing',   'elit','a','ac',       'accumsan','ad','aenean','aliquam',  'aliquet','ante','aptent','arcu',     'at','auctor','augue','bibendum', 'blandit','class','commodo','condimentum','congue',       'consequat','conubia','convallis','cras','cubilia','cum','curabitur','curae','cursus','dapibus','diam',     'dictum','dictumst','dignissim','dis',      'donec','dui','duis','egestas',  'eget','eleifend','elementum','enim',     'erat','eros','est','et',       'etiam','eu','euismod','facilisi', 'facilisis','fames','faucibus','felis',    'fermentum','feugiat','fringilla','fusce',    'gravida','habitant','habitasse','hac',      'hendrerit','himenaeos','iaculis','id',       'imperdiet','in','inceptos','integer',  'interdum','justo','lacinia','lacus',    'laoreet','lectus','leo','libero',   'ligula','litora','lobortis','luctus',   'maecenas','magna','magnis','malesuada','massa','mattis','mauris','metus',    'mi','molestie','mollis','montes',   'morbi','mus','nam','nascetur', 'natoque','nec','neque','netus',    'nibh','nisi','nisl','non',      'nostra','nulla','nullam','nunc',     'odio','orci','ornare','parturient','pellentesque','penatibus','per','pharetra', 'phasellus','placerat','platea','porta',    'porttitor','posuere','potenti','praesent', 'pretium','primis','proin','pulvinar', 'purus','quam','quis','quisque',  'rhoncus','ridiculus','risus','rutrum',   'sagittis','sapien','scelerisque','sed',      'sem','semper','senectus','sociis',   'sociosqu','sodales','sollicitudin','suscipit', 'suspendisse','taciti','tellus','tempor',   'tempus','tincidunt','torquent','tortor',   'tristique','turpis','ullamcorper','ultrices', 'ultricies','urna','ut','varius',   'vehicula','vel','velit','venenatis','vestibulum','vitae','vivamus','viverra',  'volutpat','vulputate', );
    public function word($tags = false) { return $this->words(1, $tags); }
    public function wordsArray($count = 1, $tags = false){ return $this->words($count, $tags, true); }
    public function words($count = 1, $tags = false, $array = false) { $words = array(); $word_count = 0; while ($word_count < $count) { $shuffle = true; while ($shuffle) { $this->shuffle(); if (!$word_count || $words[$word_count - 1] != $this->words[0]) { $words      = array_merge($words, $this->words); $word_count = count($words); $shuffle    = false; } } } $words = array_slice($words, 0, $count); return $this->output($words, $tags, $array); }
    public function sentence($tags = false) { return $this->sentences(1, $tags); }
    public function sentencesArray($count = 1, $tags = false) { return $this->sentences($count, $tags, true); }
    public function sentences($count = 1, $tags = false, $array = false) { $sentences = array(); for ($i = 0; $i < $count; $i++) { $sentences[] = $this->wordsArray($this->gauss(24.46, 5.08)); } $this->punctuate($sentences); return $this->output($sentences, $tags, $array); }
    public function paragraph($tags = false) { return $this->paragraphs(1, $tags); }
    public function paragraphsArray($count = 1, $tags = false) { return $this->paragraphs($count, $tags, true); }
    public function paragraphs($count = 1, $tags = false, $array = false) { $paragraphs = array(); for ($i = 0; $i < $count; $i++) { $paragraphs[] = $this->sentences($this->gauss(5.8, 1.93)); } return $this->output($paragraphs, $tags, $array, "\n\n"); }
    private function gauss($mean, $std_dev) { $x = mt_rand() / mt_getrandmax(); $y = mt_rand() / mt_getrandmax(); $z = sqrt(-2 * log($x)) * cos(2 * pi() * $y); return $z * $std_dev + $mean; }
    private function shuffle() { if ($this->first) { $this->first = array_slice($this->words, 0, 8); $this->words = array_slice($this->words, 8); shuffle($this->words); $this->words = $this->first + $this->words; $this->first = false; } else { shuffle($this->words); } }
    private function punctuate(&$sentences) { foreach ($sentences as $key => $sentence) { $words = count($sentence); if ($words > 4) { $mean    = log($words, 6); $std_dev = $mean / 6; $commas  = round($this->gauss($mean, $std_dev)); for ($i = 1; $i <= $commas; $i++) { $word = round($i * $words / ($commas + 1)); if ($word < ($words - 1) && $word > 0) { $sentence[$word] .= ','; } } } $sentences[$key] = ucfirst(implode(' ', $sentence) . '.'); } }
    private function output($strings, $tags, $array, $delimiter = ' ') { if ($tags) { if (!is_array($tags)) { $tags = array($tags); } else { $tags = array_reverse($tags); } foreach ($strings as $key => $string) { foreach ($tags as $tag) { if ($tag[0] == '<') { $string = str_replace('$1', $string, $tag); } else { $string = sprintf('<%1$s>%2$s</%1$s>', $tag, $string); } $strings[$key] = $string; } } } if (!$array) { $strings = implode($delimiter, $strings); } return $strings; }
}

function text_word($count = 1,$tag = false){ $lipsum = new LoremIpsum; return $lipsum->words($count,$tag);}
function text_paragraph($count = 1,$tag = false){ $lipsum = new LoremIpsum; return $lipsum->paragraphs($count,$tag); }
function text_sentences($count = 1,$tag = false){ $lipsum = new LoremIpsum; return $lipsum->sentences($count,$tag); }

function _text_word($count = 1,$tag = false){ $lipsum = new LoremIpsum; echo $lipsum->words($count,$tag);}
function _text_paragraph($count = 1,$tag = false){ $lipsum = new LoremIpsum; echo $lipsum->paragraphs($count,$tag); }
function _text_sentences($count = 1,$tag = false){ $lipsum = new LoremIpsum; echo $lipsum->sentences($count,$tag); }
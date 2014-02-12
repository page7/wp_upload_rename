<?php
/*
Plugin Name: wp_upload_rename
Plugin URI: http://www.nolanchou.com/wp_upload_rename/
Description: Rename upload file by random chars / numbers / date / other.
Version: 1.0.1
Author: Nolan Chou　
Author URI: http://www.nolanchou.com/
License: GUN v2
*/



if (!class_exists('wp_upload_rename'))
{
class wp_upload_rename
{

    protected $defaultOpts = array('mode'=>'char', 'length'=>5, 'param'=>'');


    // construct
    public function __construct()
    {
        if (is_admin())
        {
            $plugin = plugin_basename(__FILE__);
            add_action('admin_init', array(&$this, 'register'));
            add_action('admin_menu', array(&$this, 'menu'));
            add_filter('plugin_action_links_'.$plugin, array(&$this, 'link'));
            add_filter('wp_handle_upload_prefilter', array(&$this,'rename'));
        }
    }


    // register setting
    public function register()
    {
        register_setting('wp_upload_rename_setting', 'wp_upload_rename_options');
    }


    // Admin menu
    public function menu()
    {
        add_options_page('Upload Rename', 'Upload Rename', 'administrator', 'wp_upload_rename', array(&$this, 'setting'));
    }


    // rename
    public function rename($file)
    {
        $wp_filetype = wp_check_filetype_and_ext($file['tmp_name'], $file['name'], false);
        extract($wp_filetype);
        if (!$ext)
            $ext = ltrim(strrchr($file['name'], '.'), '.');

        $options = $this->option();
        $newname = $this -> _name($options['mode'], (int)$options['length'], (string)$options['param']).'.'.$ext;
        $file['name'] = str_replace('%file%', substr($file['name'], 0, -(strlen($ext)+1)), $newname);

        return $file;
    }



    // get new name
    protected function _name($mode, $length=5, $param='')
    {
        switch ($mode)
        {
            case 'char':
                $chars = empty($param) ? 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789' : (string)$param;
                $chars = str_shuffle($chars);
                $str = substr($chars, 0, $length);
                break;
            case 'num':
                $str = sprintf("%0{$length}d", rand(0, str_repeat('9', $length)));
                break;
            case 'date':
                $format = empty($param) ? 'Ymd_His' : $param;
                $str = date($format);
                break;
            case 'diy':
                if (empty($param))
                    return $this -> _name('char', 5);

                preg_match_all('/%((file|date|char|num)(\|[^%]+)?)%/', $param, $m);
                foreach ($m[0] as $v)
                {
                    $args = explode('|', trim($v, '%'));
                    if (in_array($args[0], array('char', 'num')))
                        $rp = $this -> _name($args[0], empty($args[1]) ? 5 : (int)$args[1]);
                    else if ( $args[0] == 'date' )
                        $rp = $this -> _name($args[0], 0, empty($args[1]) ? '' : (int)$args[1]);
                    else
                        continue;

                    $param = str_replace($v, $rp, $param);
                }
                $str = $param;
                break;
            default:
                $str = '';
        }
        return $str;
    }


    function link($links)
    {
          $settings_link = '<a href="options-general.php?page=wp_upload_rename">'.$this->__('Settings').'</a>';
          array_unshift($links, $settings_link);
          return $links;
    }



    // Setting options page.
    public function setting()
    {
        if (!current_user_can('manage_options'))
        {
            wp_die($this->__('You do not have sufficient permissions to access this page.'));
            return;
        }

        include(dirname(__FILE__).'/options.php');
    }



    // get options
    function option($key='')
    {
        $option = get_option('wp_upload_rename_options') ? get_option('wp_upload_rename_options') : array();
        $option = array_merge($this->defaultOpts, $option);
        if ($key)
            $return = $option[$key];
        else
            $return = $option;

        return $return;
    }



    //Language
    public function __($key) {
        return __($key, 'wp_upload_rename');
    }



}
}

add_action('init', 'wp_upload_rename_init');
function wp_upload_rename_init()
{
	if (class_exists('wp_upload_rename'))
    {
		new wp_upload_rename();
	}
}

//register_uninstall_hook(__FILE__, array('wp_upload_rename', 'uninstall'));


?>
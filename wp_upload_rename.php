<?php
/*
Plugin Name: wp_upload_rename
Plugin URI: http://www.nolanchou.com/wp_upload_rename/
Description: Rename upload file by random chars / numbers / date / other.
Version: 1.2
Author: Nolan Chou
Author URI: http://www.nolanchou.com/
License: GUN v2
*/

if ( ! class_exists( 'wp_upload_rename' ) ) {
	class wp_upload_rename {

		protected $defaultOpts = array(
			'mode'       => 'char',
			'length'     => 5,
			'param'      => '',
			'post_param' => '',
		);

		protected $_post = null;

		//　construct
		public function __construct() {
			$plugin = plugin_basename( __FILE__ );
			// plugin init / setting link / setting page
			add_action( 'admin_init', array( &$this, 'register' ) );
			add_action( 'admin_menu', array( &$this, 'menu' ) );
			add_filter( 'plugin_action_links_' . $plugin, array( &$this, 'link' ) );
			// rename main
			add_filter( 'wp_handle_upload_prefilter', array( &$this, 'rename' ) );
		}

		// register setting
		public function register() {
			register_setting( 'wp_upload_rename_setting', 'wp_upload_rename_options' );
		}

		// Admin menu
		public function menu() {
			add_options_page(
				'Upload Rename',
				'Upload Rename',
				'administrator',
				'wp_upload_rename',
				array(
					&$this,
					'setting',
				)
			);
		}

		// rename
		public function rename( $file ) {
			$wp_filetype = wp_check_filetype_and_ext( $file['tmp_name'], $file['name'], false );
			extract( $wp_filetype );
			if ( ! $ext ) {
				$ext = ltrim( strrchr( $file['name'], '.' ), '.' );
			}

			$options = $this->option();

			if ( ! empty( $options['post_param'] ) && ! empty( $_POST['post_id'] ) ) {
				$postid           = (int) $_POST['post_id'];
				$options['param'] = $options['post_param'];
			} else {
				$postid = 0;
			}

			$newname = $this->_name( $options['mode'], (int) $options['length'], (string) $options['param'], $postid ) . '.' . $ext;

			$file['name'] = str_replace( '%file%', substr( $file['name'], 0, - ( strlen( $ext ) + 1 ) ), $newname );

			return $file;
		}


		// get new name
		protected function _name( $mode, $length = 5, $param = '', $postid = null ) {
			switch ( $mode ) {
				case 'char':
					$chars = empty( $param ) ? 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789' : (string) $param;
					$chars = str_shuffle( $chars );
					$str   = substr( $chars, 0, $length );
					break;

				case 'num':
					$str = sprintf( "%0{$length}d", wp_rand( 0, str_repeat( '9', $length ) ) );
					break;

				case 'date':
					$format = empty( $param ) ? 'Ymd_His' : $param;
					$str    = date( $format );
					break;

				case 'title':
					if ( ! $this->_post ) {
						$this->_post = get_post( $postid );
					}

					$str = urlencode( str_replace( array( ' ', ',' ), '_', $this->_post->post_title ) );
					break;

				case 'name':
					if ( ! $this->_post ) {
						$this->_post = get_post( $postid );
					}

					$str = $this->_post->post_name;
					break;

				case 'diy':
					$reg = '/%((file|date|char|num)(\|[^%]+)?)%/';
					if ( $postid ) {
						$reg = '/%((file|date|char|num|title|name)(\|[^%]+)?)%/';
					}

					if ( empty( $param ) ) {
						return $this->_name( 'char', 5 );
					}

					preg_match_all( $reg, $param, $m );
					foreach ( $m[0] as $v ) {
						$args = explode( '|', trim( $v, '%' ) );

						if ( in_array( $args[0], array( 'char', 'num' ), true ) ) {
							$rp = $this->_name( $args[0], empty( $args[1] ) ? 5 : (int) $args[1] );
						} elseif ( 'date' === $args[0] ) {
							$rp = $this->_name( $args[0], 0, empty( $args[1] ) ? '' : (int) $args[1] );
						} elseif ( in_array( $args[0], array( 'title', 'name' ), true ) ) {
							$rp = $this->_name( $args[0], 0, '', $postid );
						} else {
							continue;
						}

						$param = str_replace( $v, $rp, $param );
					}
					$str = $param;
					break;

				default:
					$str = '';
			}

			return $str;
		}

		// add link in plugins page
		function link( $links ) {
			$settings_link = '<a href="options-general.php?page=wp_upload_rename">' . $this->__( 'Settings' ) . '</a>';
			array_unshift( $links, $settings_link );

			return $links;
		}

		// Setting options page.
		public function setting() {
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( $this->__( 'You do not have sufficient permissions to access this page.' ) );

				return;
			}

			include( __DIR__ . '/options.php' );
		}

		// get options
		function option( $key = '' ) {
			$option = get_option( 'wp_upload_rename_options' ) ? get_option( 'wp_upload_rename_options' ) : array();
			$option = array_merge( $this->defaultOpts, $option );
			if ( $key ) {
				$return = $option[ $key ];
			} else {
				$return = $option;
			}

			return $return;
		}

		//Language
		public function __( $key ) {
			return __( $key, 'wp_upload_rename' );
		}
	}
}

add_action( 'init', 'wp_upload_rename_init' );

function wp_upload_rename_init() {
	if ( class_exists( 'wp_upload_rename' ) ) {
		new wp_upload_rename();
	}
}

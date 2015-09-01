<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://www.champ.ninja/
 * @since      1.0.0
 *
 * @package    Aumd
 * @subpackage Aumd/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Aumd
 * @subpackage Aumd/public
 * @author     Champ Camba <heychampsupertramp@gmail.com>
 */
class Aumd_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	private $um_shortcode_form_id = 0;

	private $filtered_array = array();

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Aumd_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Aumd_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( 'nprogress', plugin_dir_url( __FILE__ ) . 'css/nprogress.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/aumd-public.css', array(), $this->version, 'all' );
		
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Aumd_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Aumd_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_script('nprogress', plugin_dir_url( __FILE__ ) . 'js/nprogress.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/aumd-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 *  Filter [ultimatemember] shortcode
	 */
	public function filter_um_shortcode( $out, $pairs, $atts )
	{
	   
	    

	    // something that makes the shortcode unique:
	    $found = isset( $pairs['form_id'] );
	    if ( $found )
	    {
	        // Instantly remove this filter to save processing time:
	        remove_filter( current_filter(), __FUNCTION__ );

	        // do something stunning in here!
	     }
	    return $out;
	}

	/**
	 * Add member directory ajax wrapper
	 */

	public function add_member_directory_ajax(){
		global $ultimatemember;
		$post_ID = get_queried_object_id();
		
		if($this->has_ajax_enabled( $post_ID )){
			echo "<div id=\"aumd-ajaxify-wrapper\" data-test=\"true\" data-post-id=\"".$post_ID."\"></div>";
		}else{
			echo "<!--aumd-ajaxify-wrapper no enabled -->";
		}

	}

	/**
	 * Check if ajax has enabled
	 */
	public function has_ajax_enabled( $post_ID ){
		global $wpdb;
       
       	$out = $this->get_shortcode('ultimatemember', $post_ID); 
        
        if( isset( $out[0]['form_id'] ) || ! empty( $out[0]['form_id'] ) ){
	        $form_id = filter_var($out[0]['form_id'], FILTER_SANITIZE_NUMBER_INT );
	    }else{
	    	$out = $this->get_shortcode('gravityform', $post_ID); 
        	$form_id = filter_var($out[0]['aumd_form_id'], FILTER_SANITIZE_NUMBER_INT );
        	
	    }

	    
	    $query_args["form_id"] = $form_id;
	    $has_ajax_enabled = get_post_meta( $query_args["form_id"] , '_um_ajax_settings', true);
		 return $has_ajax_enabled;
	}
	/**
	 * Ajax search directory members
	 */
	public function search_directory_members(){
		ob_start();
		
		error_reporting(0);
		global $wpdb, $ultimatemember, $wpdb, $aumd_params, $aumd_args, $aumd_query_args_dump;
       

		$inputs = isset( $_REQUEST  ) ? $_REQUEST  : null;
		$aumd_params = array();
		 $query_args = array();
		 $um_form_id = false;
		parse_str($inputs["params"], $aumd_params);
		
		$out = $this->get_shortcode('ultimatemember', $inputs["post_id"]); 
         
        if( isset( $out[0]['form_id'] ) || ! empty( $out[0]['form_id'] ) ){
	        $form_id = filter_var($out[0]['form_id'], FILTER_SANITIZE_NUMBER_INT );
	    }else{
	    	$out = $this->get_shortcode('gravityform', $inputs["post_id"]); 
        	$form_id = filter_var($out[0]['aumd_form_id'], FILTER_SANITIZE_NUMBER_INT );
        	
	    }
       
       	$query_args["form_id"] = $form_id;

	    $has_ajax_enabled = get_post_meta( $query_args["form_id"] , '_um_ajax_settings');
		$query_args["enabled_ajax"] = $has_ajax_enabled;
		

	    add_filter('um_prepare_user_query_args', array($this,'aumd_query_args'), 50, 2);
	    
	    $post_data = $ultimatemember->query->post_data( $form_id );
		$query_args['profiles_per_page'] = $post_data['profiles_per_page'];
	    $query_args['profiles_per_page_mobile'] = $post_data['profiles_per_page_mobile'];
	    if( isset( $aumd_params['members_page'] ) ){
	    	$_REQUEST['members_page'] = $aumd_params['members_page'];
	    }
	    $ultimatemember->members->results = $ultimatemember->members->get_members( $query_args );
	    $data_query_args = $query_args;
		
		
		$query_args = $ultimatemember->members->results;

		um_members_directory_display( $aumd_args );
		$html = ob_get_contents();
		ob_end_clean();
		
			
		

      wp_send_json(array( "post_data" => $post_data, "dump" => $this->filtered_array, "html" => $html,"result" => $query_args, 'result_after' =>$data_query_args  ));
	
	}

	public function get_shortcode( $shortcode, $post_id ){
		global $wpdb;

		$post_rs = $wpdb->get_row($wpdb->prepare("SELECT post_content FROM {$wpdb->prefix}posts WHERE ID = %d", $post_id) );

        $pattern = get_shortcode_regex();

        $tag = $shortcode;

		preg_match_all( '/' . get_shortcode_regex() . '/s', $post_rs->post_content, $matches );
	    $out = array();
	    if( isset( $matches ) )
	    {
			foreach( $matches as $match  ){    	
		       
		        foreach( (array) $match as $key )
		        {
		        	if( strpos($key,  $tag) ){
		               $out[] = shortcode_parse_atts( $key );  
		        	}
		        }

		    }
	    }

	    return $out;
	}

	public function aumd_query_args($query_args, $args){
		
		global $ultimatemember, $aumd_params, $aumd_args,$aumd_query_args_dump,$um_form_id;
		/***
		** Prepare Directory
		***/
			$shortcodes = new UM_Shortcodes;
			
			$defaults = array();
			$args = wp_parse_args( $args, $defaults );
			
			// when to not continue
			$shortcodes->form_id = (isset($args['form_id'])) ? $args['form_id'] : null;
			if (!$shortcodes->form_id) return;
			$shortcodes->form_status = get_post_status( $shortcodes->form_id );
			if ( $shortcodes->form_status != 'publish' ) return;
			
			// get data into one global array
			$post_data = $ultimatemember->query->post_data( $shortcodes->form_id );
			
			if ( !isset( $args['template'] ) ) $args['template'] = '';
			
			if ( isset( $post_data['template'] ) && $post_data['template'] != $args['template']) $args['template'] = $post_data['template'];
			
			if ( !$shortcodes->template_exists( $args['template'] ) ) $args['template'] = $post_data['mode'];
			
			if ( !isset( $post_data['template'] ) ) $post_data['template'] = $post_data['mode'];
			$args = array_merge( $post_data, $args );
			
			if ( isset( $args['use_globals'] ) && $args['use_globals'] == 1 ) {
				$query_args = array_merge( $args, $shortcodes->get_css_args( $args ) );
			} else {
				$query_args = array_merge( $shortcodes->get_css_args( $args ), $args );
			}
      
      		
			 
			extract( $args, EXTR_SKIP );
			

			// for profiles only
			if ( $mode == 'profile' && um_profile_id() && isset( $query_args['role'] ) && $query_args['role'] && 
					$query_args['role'] != $ultimatemember->query->get_role_by_userid( um_profile_id() ) )
				return;

		/***
		** Prepare User Query
		**/

			$query_args['fields'] = 'ID';
			
			$query_args['number'] = 0;
			
			$query_args['meta_query']['relation'] = 'AND';
			
			// must have a profile photo
			if ( $has_profile_photo == 1 ) {
				$query_args['meta_query'][] = array(
					'key' => 'profile_photo',
					'value' => '',
					'compare' => '!='
				);
			}
			
			// must have a cover photo
			if ( $has_cover_photo == 1 ) {
				$query_args['meta_query'][] = array(
					'key' => 'cover_photo',
					'value' => '',
					'compare' => '!='
				);
			}
			
			// add roles to appear in directory 
			if ( !empty( $roles ) ) {
			
				$query_args['meta_query'][] = array(
					'key' => 'role',
					'value' => unserialize($roles),
					'compare' => 'IN'
				);
			
			}
			
			// sort members by
			$query_args['order'] = 'ASC';
			
			if ( isset( $sortby ) ) {
				
				if ( $sortby == 'other' && $sortby_custom ) {
				
					$query_args['meta_key'] = $sortby_custom;
					$query_args['orderby'] = 'meta_value';
					
				} else if ( in_array( $sortby, array( 'last_name', 'first_name' ) ) ) {
				
					$query_args['meta_key'] = $sortby;
					$query_args['orderby'] = 'meta_value';
					
				} else {
				
					if ( strstr( $sortby, '_desc' ) ) {$sortby = str_replace('_desc','',$sortby);$order = 'DESC';}
					if ( strstr( $sortby, '_asc' ) ) {$sortby = str_replace('_asc','',$sortby);$order = 'ASC';}
					$query_args['orderby'] = $sortby;
				
				}
				
				if ( isset( $order ) ) {
					$query_args['order'] = $order;
				}
				
				$query_args = apply_filters('um_modify_sortby_parameter', $query_args, $sortby);

			}
		

		/***
		** Add Search to Query
		***/
			if ( isset(  $aumd_params['um_search'] ) ) {
				
				$query = $aumd_params;
				//$query["members_page"] = 1;
				foreach( $query as $field => $value ) {
					if(in_array($field, array('members_page'))) continue;
					
					if ( in_array( $field, array('gender') ) ) {
						$operator = '=';
					} else {
						$operator = 'LIKE';
					}

					if ( in_array( $ultimatemember->fields->get_field_type( $field ), array('checkbox','multiselect') ) ) {
						$operator = 'LIKE';
					}
					if ( $value && $field != 'um_search' && $field != 'page_id' ) {
						
						if ( !in_array( $field, $ultimatemember->members->core_search_fields ) ) {
							
							if ( strstr($field, 'role_' ) ) {
								$field = 'role';
								$operator = '=';
							}
							
							$query_args['meta_query'][] = array(
								'key' => $field,
								'value' => $value,
								'compare' => $operator,
							);
						
						}
					
					}
					
				}

			}
			
		if ( count ($query_args['meta_query']) == 1 ) {
			unset( $query_args['meta_query'] );
		}


		/***
		** Search User by Username/Emails
		***/
			$query = $aumd_params;
			foreach( $ultimatemember->members->core_search_fields as $key ) {
				if ( isset( $query[$key] ) ) {
					$query_args['search']         = '*' . $query[$key] . '*';
				}
			}

		/***
		** Remove special users from list
		***/
			if ( !um_user_can('can_edit_everyone') ) {
			
				$query_args['meta_query'][] = array(
					'key' => 'account_status',
					'value' => 'approved',
					'compare' => '='
				);
				
			}
			
			$query_args['meta_query'][] = array(
				'key' => 'hide_in_members',
				'value' => '',
				'compare' => 'NOT EXISTS'
			);
		
		$aumd_args = $query_args;

		$this->filtered_array = $aumd_args;

		return $query_args;
	}


}

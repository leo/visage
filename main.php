<?php


/*
	* Plugin Name: Visage
	* Plugin URI: https://wordpress.org/plugins/visage/
	* Description: Allow users to set their own avatars easily.
	* Text Domain: visage
	* Domain Path: /lang/
	* Version: 1.0.5
	* Author: Leonard Lamprecht
	* Author URI: https://profiles.wordpress.org/mindrun/#content-plugins
	* License: GPLv2
*/


namespace visage;

class load {

	public function __construct() {

		$actions = array(

			'plugins_loaded',
			'admin_enqueue_scripts',
			'admin_notices',

			'user_profile' => array(
				'show_user_profile',
				'edit_user_profile'
			),

			'save_avatar' => array(
				'personal_options_update',
				'edit_user_profile_update'
			)

		);

		foreach( $actions as $key => $action ) {

			if( is_array( $action ) ) {

				foreach( $action as $int => $handle ) {
					add_action( $handle, array( $this, $key ) );
				}

			} else {
				add_action( $action, array( $this, $action ) );
			}

		}

		add_filter( 'get_avatar', array( $this, 'get_avatar' ), 1 , 4 );

	}

	private function calc_ratings() {

		$user_id = get_current_user_id();
		$ratings = array( 'G', 'PG', 'R', 'X' );

	    $array_pos_user = array_search( get_user_meta( $user_id, 'visage_rating', true ), $ratings );
	    $array_pos_global = array_search( get_option( 'avatar_rating' ), $ratings );

	    if( $array_pos_global >= $array_pos_user ) {
			return true;
		} else {
			return false;
		}

	}

	public function admin_notices() {

		if( $this->calc_ratings() == false && isset( $_GET['updated'] ) ) {

			echo '<div class="error"><p>';

			$warning = __( '%1$sWarning:%2$s The default avatar rating is currently set to "%3$s", and your personal avatar has the rating "%4$s".', 'visage' );

			printf( $warning, '<b>', '</b>', get_option( 'avatar_rating' ), get_user_meta( get_current_user_id(), 'visage_rating', true ) );

			echo '</p></div>';

		}

	}

	public function plugins_loaded() {

		$meta = array(
			'description' => __( 'Allow users to set their own avatars easily.', 'visage' )
		);

		load_plugin_textdomain( 'visage', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );

	}

	public function admin_enqueue_scripts( $page ) {

		if ( 'profile.php' != $page && 'user-edit.php' != $page ) {
	        return;
	    }

		wp_enqueue_media();
		wp_enqueue_script( 'visage', plugins_url( 'assets/admin.js', __FILE__ ) );
		wp_enqueue_style( 'visage', plugins_url( 'assets/admin.css', __FILE__ ) );

		$translations = array(
			'media_title' => __( 'Choose a custom avatar', 'visage' ),
			'media_button' => __( 'Select', 'visage' )
		);

		wp_localize_script( 'visage', 'visage_lang', $translations );

	}

	public function get_avatar( $avatar, $id_or_email, $size, $default ) {

		global $pagenow;

		$plain_avatar = $avatar;
		$user = false;

		if( is_numeric( $id_or_email ) ) {

			$id = (int) $id_or_email;
			$user = get_user_by( 'id' , $id );

	        } elseif ( is_object( $id_or_email ) ) {

	            if ( ! empty( $id_or_email->user_id ) ) {
	                $id = (int) $id_or_email->user_id;
	                $user = get_user_by( 'id' , $id );
	            }

	    } else {
	        $user = get_user_by( 'email', $id_or_email );
	    }

	    if( is_object( $user ) ) {
		    $meta = get_user_meta( $user->ID, 'visage_id', true );
	    }

	    if( $this->calc_ratings() == true || $pagenow == 'profile.php' && $size != 26 || $pagenow == 'user-edit.php' && $size != 26 ) {

		    $rating_allowed = true;

		} else {

			$rating_allowed = false;

		}

	    if ( $user && is_object( $user ) && $meta && $rating_allowed == true ) {

			if ($pagenow == 'options-discussion.php' && $size != 32 || $pagenow != 'options-discussion.php' ) {

				switch( $size ) {

					case( $size <= 150 ):
						$tag = 'thumbnail';
					case( $size > 150 && $size <= 300 ):
						$tag = 'medium';
					case( $size > 300 ):
						$tag = 'larger';

					default: $tag = 'thumbnail';

				}

				$url = wp_get_attachment_image_src( $meta, $tag );
				$avatar = '<img alt="visage" src="'. $url[0] .'" class="avatar avatar-'. $size .' photo" height="'. $size .'" width="'. $size .'" />';

			}

	    }

	    return $avatar;

	}

	public function user_profile( $user ) {

		$avatar = get_avatar( $user->ID );

		if( $avatar ) {

			$visage_meta = get_user_meta( $user->ID, 'visage_id', true );
			$avatar_id = 0;

			$avatar_id = $visage_meta ?: $avatar_id;

			$default_avatar = 'http://gravatar.com/avatar/'. md5( strtolower( $user->user_email ) ) . '?default='. get_option( 'avatar_default' ) .'&s=96';

			include( 'option.php' );

		}

	}

	public function save_avatar( $user_id ) {

		$avatar_id = sanitize_text_field( $_POST['visage-current'] );

		if( $avatar_id != 0 ) {

			update_user_meta( $user_id, 'visage_id', $avatar_id );
			update_user_meta( $user_id, 'visage_rating', sanitize_text_field( $_POST['visage-rating'] ) );

		} else {

			delete_user_meta( $user_id, 'visage_id' );
			delete_user_meta( $user_id, 'visage_rating' );

		}

	}

}

new load;

?>
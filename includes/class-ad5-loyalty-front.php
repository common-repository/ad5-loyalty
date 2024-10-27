<?php
/**
 * Processor for front
 * 
 * @category Plugin Core
 * @package AD5 LOYALTY
 * @author AD5
 */
class AD5_Loyalty_Front
{
    private $message = array();
    private $setting = array();
    private $admin;
    
    /**
     * init
     */
    public function init()
    {
        if ( ! class_exists( 'AD5_Loyalty_Admin' ) ) {
            require_once( dirname(__FILE__) . '/class-ad5-loyalty-admin.php' );
        }
        $this->admin = new AD5_Loyalty_Admin();

        add_filter( 'the_content', array( $this, 'content' ), 1 );
        add_action( 'wp_footer', array( $this, 'display_modal' ) );
        add_action( 'wp_footer', array( $this, 'display_css' ) );
        add_action( 'init', array( $this, 'action' ) );
        add_action( 'wp_ajax_ad5_loyalty_front', array( $this, 'process' ) );
        add_action( 'wp_ajax_nopriv_ad5_loyalty_front', array( $this, 'process' ) );
        add_shortcode( 'loyalty_button_login', array( $this, 'shortcode_loyalty_button_login' ));
        add_shortcode( 'loyalty_button_register', array( $this, 'shortcode_loyalty_button_register' ));
        add_shortcode( 'loyalty_button_logout', array( $this, 'shortcode_loyalty_button_logout' ));
		add_filter( 'show_admin_bar' , array( $this, 'hide_admin_bar' ) );
		add_action( 'auth_redirect', array( $this, 'subscriber_redirect' ) );
    }

    /**
     * FILTER HOOK : show_admin_bar
     * hide admin bar for subscriber
     */
	public function hide_admin_bar( $content ) {
		$user = wp_get_current_user();
		if ( empty( $user->roles ) || in_array( 'subscriber', $user->roles ) ) {
            return false;   
        } else {
            return $content;
        }
	}

    /**
     * ACTION HOOK : auth_redirect
     * deny subscriber to access dashboard
     */
	public function subscriber_redirect( $user_id ) {
        $user = get_userdata( $user_id );
		if ( empty( $user->roles ) || in_array( 'subscriber', $user->roles ) ) {
			wp_safe_redirect( home_url() );
			exit();
		}
    }

    /**
     * get option for this plugin
     */
    public function get_setting( $key = null )
    {
        return $this->admin->get_setting( $key );
    }

    /**
     * modify content when user is / is not logged in
     */
    public function content( $content )
    {
        global $post;

        $contents['original'] = $content;

        if ( is_user_logged_in() ) {
            $user = get_post_meta( $post->ID, 'ad5_loyalty_content_user', true );
            if ( ! $user ) {
                $user = get_option( 'ad5_loyalty_default_content_user' );
            }
            if ( $user ) {
                $contents['user'] =  $user;
            }
        } else {
            $guest = get_post_meta( $post->ID, 'ad5_loyalty_content_guest', true );
            if ( ! $guest ) {
                $guest = get_option( 'ad5_loyalty_default_content_guest' );
            }
            if ( $guest ) {
                $contents['guest'] =  $guest;
            }
        }
        $contents = apply_filters( 'ad5-loyalty-content', $contents );
        
        return implode( "\n", $contents );
    }
    
    /**
     * display momdal window
     */
    public function display_modal()
    {
        include( dirname(__FILE__) . '/../template/modal.php' );
    }

    public function display_css()
    {
        if ( ! class_exists( 'AD5_Colors' ) ) {
            require_once( dirname(__FILE__) . '/class-ad5-colors.php' );
        }
        $colors = new AD5_Colors;
        $primary = $this->get_setting( 'color_button_primary' );
        $secondary = $this->get_setting( 'color_button_secondary' );
        $colors->set_hex( $primary );
        $primary_border = $colors->get_darken( 0.3 );
        $primary_text = $colors->get_counter_threshold();        
        $colors->set_hex( $secondary );
        $secondary_border = $colors->get_darken( 0.3 );
        $secondary_text = $colors->get_counter_threshold();    

        $css = "<style>";
        $css .= ".ad5-loyalty-button-style-primary {display:inline-block; padding:10px 30px; text-decoration:none; border-radius:5px;  background: {$primary};  color: {$primary_text}; border-bottom: 1px solid {$primary_border};} ";
        $css .= ".ad5-loyalty-button-style-primary:hover {text-decoration:none; color: {$primary_text};} ";
        $css .= ".ad5-loyalty-button-style-secondary {display:inline-block; padding:10px 30px; text-decoration:none; border-radius:5px;  background: {$secondary};  color: {$secondary_text}; border-bottom: 1px solid {$secondary_border};} ";
        $css .= ".ad5-loyalty-button-style-secondary:hover {text-decoration:none; color: {$secondary_text};} ";
        $css .= "</style>";
        echo $css;
    }

    /**
     * show message or redirect according to 'ad5_loyalty_action' parameter
     */
    public function action()
    {
        if ( ! empty( $_GET['ad5_loyalty_action'] ) ) {
            $action = $_GET['ad5_loyalty_action'];
            if ( $action == 'registered' ) {
                $this->message['header'] = $this->t( 'Sign Up' );
                $this->message['success'] = $this->t( 'Registered successfully' );
                $this->message['body'] = '';
            }
            if ( $action == 'signedin' ) {
                $this->message['header'] = $this->t( 'Sign In' );
                $this->message['success'] = $this->t( 'Signed in successfully' );
                $this->message['body'] = '';
            }
            if ( $action == 'signout' ) {
                wp_logout();
                wp_safe_redirect( home_url() . '?ad5_loyalty_action=signedout' );
                exit();
            }
            if ( $action == 'signedout' ) {
                $this->message['header'] = $this->t( 'Sign Out' );
                $this->message['success'] = $this->t( 'Signed out successfully' );
                $this->message['body'] = '';
            }
            if ( $action == 'register_disabled' ) {
                $this->message['header'] = $this->t( 'Sign Up' );
                $this->message['error'] = $this->t( 'New member not accesptable' );
                $this->message['body'] = '';
            }
            $this->message = apply_filters( 'ad5-loyalty-action', $this->message, $action );
        }
    }

    public function get_message()
    {
        return $this->message;
    }
    
    /**
     * processing ajax request
     */
    public function process()
    {
        $data = ! empty( $_POST['data'] ) ? $_POST['data'] : array();
        $process = ! empty( $data['process'] ) ? 'process_' . $data['process'] : null;
        if ( $process && method_exists( $this, $process ) ) {
            $return = $this->$process( $data );
            $return['valid'] = true;
        } else {
            $return['valid'] = false;
        }
        $return = apply_filters( 'ad5-loyalty-process', $return, $process, $data );
        header( 'content-type: application/json; charset: utf-8' );
        echo json_encode( $return );
        die();
    }

    /**
     * processing ajax request of sign in
     */
    public function process_signin( $data ) {
        if ( ! wp_verify_nonce( $data['_wpnonce'], 'ad5-loyalty-signin' ) ) {
            return array( 
                'success' => false,
                'errors' => array( 'error_global' => $this->t( 'Invalid transition' ) ),
                'message' => "",
                'action' => ""
            );
        }

        if ( ! class_exists( 'AD5_Form' ) ) {
            require_once( dirname(__FILE__) . '/class-ad5-form.php' );        
        }
        $form = new AD5_Form( 'ad5-loyalty' );
		$form->set_fields( array( 
			'user_email' => array( 
				'validate' => array( AD5_Form::VALIDATE_REQUIRED, AD5_Form::VALIDATE_EMAIL ),
				'filter' => array(),
			 ),
			'user_pass' => array( 
				'validate' => array( AD5_Form::VALIDATE_REQUIRED ),
				'filter' => array(),
			 ),
		) );

        //validate
        $action = "";
        $message = "";
		$form->set_post_data( $data );
		$form->validate();
		if ( ! $form->has_error() ) {
            //log in
            if ( ! class_exists( 'AD5_Auth' ) ) {
                require_once( dirname(__FILE__) . '/class-ad5-auth.php' ); 
            }
            $auth = new AD5_Auth();
            if ( $auth->sign_in( $form->get_data( 'user_email' ), $form->get_data( 'user_pass' ) ) ) {
                $action = "signedin";                
            } else {
				$form->set_error( AD5_Form::ERROR_GLOBAL, $this->t('Invalid email or password') );
			}
		}

		return array( 
            'success' => ! $form->has_error(),
            'errors' => $form->get_errors(),
            'message' => $message,
            'action' => $action
		);
    }
    
    /**
     * processing ajax request of register
     */
    public function process_register( $data )
    {
        $action = "";
        $message = "";
        if ( ! wp_verify_nonce( $data['_wpnonce'], 'ad5-loyalty-register' ) ) {
            return array( 
                'success' => false,
                'errors' => array( 'error_global' => $this->t( 'Invalid transition' ) ),
                'message' => "",
                'action' => ""
            );
        }

        if ( $this->get_setting('register_disabled') ) {
            return array( 
                'success' => false,
                'errors' => array(),
                'message' => "",
                'action' => "register_disabled"
            );
        }

        if ( ! class_exists( 'AD5_Form' ) ) {
            require_once( dirname(__FILE__) . '/class-ad5-form.php' );        
        }
        $form = new AD5_Form( 'ad5-loyalty' );
		$form->set_fields( array( 
			'user_email' => array( 
				'validate' => array( AD5_Form::VALIDATE_REQUIRED, AD5_Form::VALIDATE_EMAIL ),
				'filter' => array(),
			 ),
			'user_pass' => array( 
				'validate' => array( AD5_Form::VALIDATE_REQUIRED, AD5_Form::VALIDATE_ALPHANUMERIC, AD5_Form::VALIDATE_MIN( 6 ) ),
				'filter' => array(),
			 ),
			'nickname' => array( 
				'validate' => array( AD5_Form::VALIDATE_REQUIRED ),
				'filter' => array( AD5_Form::FILTER_HTML ),
			 )
		) );

        //validate
		$form->set_post_data( $data );
		$form->validate();
		if ( ! $form->has_error() ) {
			//add user
            if ( ! class_exists( 'AD5_User_Manager' ) ) {
                require_once( dirname(__FILE__) . '/class-ad5-user-manager.php' );        
            }
			$manager = new AD5_User_Manager( 'ad5-loyalty' );
			$manager->set_data( 'user_email', $form->get_data( 'user_email' ) );
			$manager->set_data( 'user_pass', $form->get_data( 'user_pass' ) );
			$manager->set_data( 'nickname', $form->get_data( 'nickname' ) );
			$login = 's' . date( 'YmdHis' ) .  sprintf( '%03d', mt_rand( 0,999 ) );
			$manager->set_data( 'user_login', $login );
			$manager->set_data( 'role', 'subscriber' );
			
			if ( $manager->insert() ) {
                $action = "registered";
                //log in
                if ( ! class_exists( 'AD5_Auth' ) ) {
                    require_once( dirname(__FILE__) . '/class-ad5-auth.php' );
                }
                $auth = new AD5_Auth();
                $auth->sign_in( $form->get_data( 'user_email' ), $form->get_data( 'user_pass' ) );
            } else {
				$form->set_errors( $manager->get_error() );
			}
		}

		return array( 
            'success' => ! $form->has_error(),
            'errors' => $form->get_errors(),
            'message' => $message,
            'action' => $action
		);
    }

    /**
     * shortcodes
     */
    public function shortcode_loyalty_button_login($attr)
    {
        $attr = shortcode_atts(
            array(
                'class' => 'ad5-loyalty-button-style-secondary',
                'text' => $this->t( 'Sign In' ),
            ),
            $attr,
            'loyalty_button_login'
        );
        $output = '<a href="#ad5-loyalty-signin" class="ad5-loyalty-button ' . $attr['class'] . '">' . $attr['text'] . '</a>';
        return $output;
    }

    public function shortcode_loyalty_button_register($attr)
    {
        $attr = shortcode_atts(
            array(
                'class' => 'ad5-loyalty-button-style-primary',
                'text' => $this->t( 'Sign Up' ),
            ),
            $attr,
            'loyalty_button_register'
        );
        $output = '<a href="#ad5-loyalty-register" class="ad5-loyalty-button ' . $attr['class'] . '">' . $attr['text'] . '</a>';
        return $output;
    }

    public function shortcode_loyalty_button_logout($attr)
    {
        $attr = shortcode_atts(
            array(
                'class' => 'ad5-loyalty-button-style-secondary',
                'text' => $this->t( 'Sign Out' ),
            ),
            $attr,
            'loyalty_button_logout'
        );
        $output = '<a href="?ad5_loyalty_action=signout" class="' . $attr['class'] . '">' . $attr['text'] . '</a>';
        return $output;
    }

    /**
     * translate
     */
    public function t( $str )
    {
        return __( $str, 'ad5-loyalty' );
    }

    public function e( $str )
    {
        echo __( $str, 'ad5-loyalty' );
    }
}


<?php
/**
 * Processor for admin page
 * 
 * @category Plugin Core
 * @package AD5 LOYALTY
 * @author AD5
 */
class AD5_Loyalty_Admin
{
    private $setting = array();

    /**
     * init
     */
    public function init()
    {
        add_action( 'admin_menu', array( $this, 'add_page' ) );
        add_action( 'admin_init', array( $this, 'register_setting' ) );
        add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
        add_action( 'save_post', array( $this, 'save_content' ) );
    }

    /**
     * add admin setting pages
     */
    public function add_page()
    {
        $page = add_menu_page( 'LOYALTY', 'LOYALTY', 'manage_options', 'ad5-loyalty-option', array( $this, 'display_option_page' ), null, 71 );
        add_action( "admin_head-" . $page, function () {
            wp_enqueue_script( 'jscolor', plugins_url( '/../resource/jscolor.min.js', __FILE__ ) );
        });
        add_submenu_page( 'ad5-loyalty-option', $this->t( 'General Setting' ), $this->t( 'General Setting' ), 'manage_options', 'ad5-loyalty-option', array( $this, 'display_option_page' ) );
        add_submenu_page( 'ad5-loyalty-option', $this->t( 'Documentation' ), $this->t( 'Documentation' ), 'manage_options', 'ad5-loyalty-docs', array( $this, 'display_docs_page' ) );
        do_action('ad5-loyalty-add-page');
    }

    public function display_option_page()
    {
        include( dirname(__FILE__) . '/../template/admin-option.php' );
    }

    public function display_docs_page()
    {
        include( dirname(__FILE__) . '/../template/admin-docs.php' );
    }

    public function register_setting()
    {
        $options = array('ad5_loyalty_setting', 'ad5_loyalty_default_content_guest', 'ad5_loyalty_default_content_user');
        foreach( $options as $option ) {
            register_setting( 'ad5-loyalty-option-group', $option );
        }
        do_action( 'ad5-loyalty-register-setting' );
    }

    /**
     * get option for this plugin
     */
    public function get_setting( $key = null )
    {
        if ( ! $this->setting ) {
            $this->setting = get_option( 'ad5_loyalty_setting' );
        }
        if ( $key ) {
            if ( ! empty( $this->setting[$key] ) ) {
                return $this->setting[$key];
            } else {
                if ( $key == 'color_button_primary' ) {
                    return '#333333';
                } elseif ( $key == 'color_button_secondary' ) {
                    return '#666666';
                } else {
                    return null;
                }
            }
        } else {
            return $this->setting;
        }
    }

    /**
     * add metabox on edit-post page
     */
    public function add_meta_box()
    {
        add_meta_box(
            'ad5-loyalty-content',
            $this->t( 'Loyalty Contents' ),
            array( $this, 'display_meta_box' ),
            null,
            'normal',
            'high'
        );        
    }

    public function display_meta_box()
    {
        wp_nonce_field( 'ad5_loyalty_nonce', 'ad5_loyalty_nonce' );
        global $post;
        $ad5_loyalty_content_guest = get_post_meta( $post->ID, 'ad5_loyalty_content_guest', true );
        $ad5_loyalty_content_user = get_post_meta( $post->ID, 'ad5_loyalty_content_user', true );
        echo '<p>' . $this->t( 'Contents below will be outputted after main content.' ) . '</p>';
        echo '<p>' . $this->t( 'To display totally different contents for guests and members, leave main content blank.' ) . '</p>';
        echo '<h4>' . $this->t( 'For guests' ) . '</h4>';
        //wp_editor( esc_attr( $ad5_loyalty_content_guest ), 'ad5_loyalty_content_guest' );
        wp_editor( $ad5_loyalty_content_guest, 'ad5_loyalty_content_guest' );
        echo '<h4>' . $this->t( 'For members' ) . '</h4>';
        //wp_editor( esc_attr( $ad5_loyalty_content_user ), 'ad5_loyalty_content_user' );
        wp_editor( $ad5_loyalty_content_user, 'ad5_loyalty_content_user' );
        do_action( 'ad5-loyalty-admin-meta-box' );
    }

    function save_content( $post_id ) {
        if ( ! isset( $_POST['ad5_loyalty_nonce'] ) ) {
            return;
        }
        if ( ! wp_verify_nonce( $_POST['ad5_loyalty_nonce'], 'ad5_loyalty_nonce' ) ) {
            return;
        }
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {
            if ( ! current_user_can( 'edit_page', $post_id ) ) {
                return;
            }
        }
        else {
            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                return;
            }
        }
        if ( isset( $_POST['ad5_loyalty_content_guest'] ) ) {
            $input = $_POST['ad5_loyalty_content_guest'];
            update_post_meta( $post_id, 'ad5_loyalty_content_guest', $input );
        }
        if ( isset( $_POST['ad5_loyalty_content_guest'] ) ) {
            $input = $_POST['ad5_loyalty_content_user'];
            update_post_meta( $post_id, 'ad5_loyalty_content_user', $input );
        }
        do_action( 'ad5-loyalty-save-content', $post_id );
    }

    /**
     * display menu for admin setting page
     */
    public static function admin_page_menu( $current = null )
    {
        $pages = array(
            'ad5-loyalty-option' => __( 'General Setting', 'ad5-loyalty' ),
            'ad5-loyalty-docs' => __( 'Documentation', 'ad5-loyalty' ),
        );
        $pages = apply_filters( 'ad5-loyalty-admin-page-menu' , $pages );
        $output = '<div class="wp-filter">';
        $output .= '<ul class="filter-links">';
        foreach ( $pages as $slug => $page ) {
            $class = ( $slug == $current ? 'current' : '' );
            $output .= '<li class=""><a href="?page=' . $slug . '" class="' . $class . '">' . $page . '</a></li>';
        }
        $output .= '</ul>';
        $output .= '</div>';
        echo $output;
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


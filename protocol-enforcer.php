<?php

/**
 * Plugin Name: Protocol Enforcer
 * Description: Plugin to allow user to specify by post, whether to force http or https.
 * Author: KCPT
 * Author URI: http://www.KCPT.org
 * License: GPLv2 or later
 * Version: 0.3
 * Text Domain: protocolenforcer
 */
class KCPT_Protocol_Enforcer
{

    public $slug = "kcpt-force-protocol";
    public $url = false;

    public function __construct()
    {

        add_action( 'add_meta_boxes', [ $this, 'metabox' ] );
        add_action( 'save_post', [ $this, 'saveMetabox' ] );
        add_action( 'template_redirect', [ $this, 'redirect' ] );

    }

    public function metabox()
    {

        add_meta_box( $this->slug, __( 'Force Protocol', 'protocolenforcer' ), [ $this, 'viewMetabox' ], null, 'side', 'high' );

    }

    public function redirect()
    {

        if (is_home() or is_category() or is_archive() or is_feed())
            return;

        global $post;

        if ( ! isset( $post ) or ! isset( $post->ID ))
            return;

        $meta     = get_post_custom( $post->ID );
        $option   = false;
        $protocol = "http";

        if (isset( $meta[ 'forceprotocol' ][ 0 ] ) and ! empty( $meta[ 'forceprotocol' ][ 0 ] )) {
            $option = $meta[ 'forceprotocol' ][ 0 ];
        }

        if ( ! $option) return;

//        var_dump( $GLOBALS ); die;

        if (isset( $_SERVER[ 'HTTPS' ] ) and $_SERVER[ 'HTTPS' ] == "on")
            $protocol = "https";

        if (isset( $_SERVER[ 'REQUEST_SCHEME' ] ) and $_SERVER[ 'REQUEST_SCHEME' ] == "https")
            $protocol = "https";

        switch ( $option ) {
            case 'http':
                if ($protocol == "https") {
                    $URL = $option . '://' . $_SERVER[ 'HTTP_HOST' ] . $_SERVER[ 'REQUEST_URI' ];
                    $this->url = $URL;
                    wp_safe_redirect( $URL, 301 );
                    exit;
                }
                break;
            case 'https':
                if ($protocol == "http") {
                    $URL = $option . '://' . $_SERVER[ 'HTTP_HOST' ] . $_SERVER[ 'REQUEST_URI' ];
                    $this->url = $URL;
                    wp_safe_redirect( $URL, 301 );
                    exit;
                }
                break;
        }

        return;


    }

    public function saveMetabox( $postID )
    {

        global $post;

        if ( ! isset( $_POST[ 'kcpt_metabox_protocol_nonce' ] ) || ! wp_verify_nonce( $_POST[ 'kcpt_metabox_protocol_nonce' ],
                basename( __FILE__ ) )
        ) {
            return $postID;
        }

        if (
            ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ||
            ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ||
            isset( $_REQUEST[ 'bulk_edit' ] ) ||
            ( isset( $post->post_type ) && $post->post_type == 'revision' ) ||
            ( ! current_user_can( 'edit_post', $postID ) )
        ) {
            return $postID;
        }

        if ( ! isset( $_POST[ 'forceprotocol' ] ))
            return $postID;

        $option = sanitize_text_field( $_POST[ 'forceprotocol' ] );

        if ( ! empty( $option ))
            update_post_meta( $postID, 'forceprotocol', $option );

        return $postID;

    }

    public function viewMetabox()
    {

        global $post;

        $meta = get_post_custom( $post->ID );


        $option = "default";

        if (isset( $meta[ 'forceprotocol' ][ 0 ] ) and ! empty( $meta[ 'forceprotocol' ][ 0 ] )) {
            $option = $meta[ 'forceprotocol' ][ 0 ];
        }

//        var_dump( $meta );

        ?>
        <input type="hidden" name="kcpt_metabox_protocol_nonce"
               value="<?= wp_create_nonce( basename( __FILE__ ) ); ?>"/>
        <select name="forceprotocol">
            <option value="default" <?php if ( $option == "default" ): ?>selected="selected"<?php endif; ?>>Default
            </option>
            <option value="http" <?php if ( $option == "http" ): ?>selected="selected"<?php endif; ?>>HTTP</option>
            <option value="https" <?php if ( $option == "https" ): ?>selected="selected"<?php endif; ?>>HTTPS</option>
        </select>
        <?php

    }

}

$KCPTProtocolEnforcer = new KCPT_Protocol_Enforcer();
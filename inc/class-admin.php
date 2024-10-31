<?php

/**
 * Admin Class
 *
 * @package     Wow_Plugin
 * @subpackage  Admin
 * @author      Dmytro Lobov <d@dayes.dev>
 * @copyright   2020 Dayes
 * @license     GNU Public License
 * @version     1.0
 */

namespace woo_product_faq;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class WP_Plugin_Admin {
	/**
	 * Setup to admin panel of the plugin
	 *
	 * @param array $info general information about the plugin
	 *
	 * @since 1.0
	 */
	public function __construct( $info ) {
		$this->plugin = $info['plugin'];

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_metbox' ) );
		add_action( 'save_post', array( $this, 'save_metbox' ) );
	}


	public function admin_scripts() {

		global $post;

		if ( is_object( $post ) && $post->post_type != 'product' ) {
			return;
		}

		$pre_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_style( $this->plugin['slug'] . '-admin', $this->plugin['url'] . 'assets/admin' . $pre_suffix . '.css' );

		wp_enqueue_script( $this->plugin['slug'] . '-admin', $this->plugin['url'] . 'assets/admin' . $pre_suffix . '.js', array( 'jquery' ), null, true );

		wp_localize_script( $this->plugin['slug'] . '-admin', $this->plugin['prefix'] . '_object', array(
			'title'    => esc_attr__( 'FAQ', 'woo-product-faq' ),
			'remove'   => esc_attr__( 'Remove', 'woo-product-faq' ),
			'input'    => esc_attr__( 'Enter FAQ title', 'woo-product-faq' ),
			'textarea' => esc_attr__( 'Enter FAQ description', 'woo-product-faq' ),
			'prefix'   => $this->plugin['prefix'],

		) );

		wp_enqueue_script( 'jquery-ui-sortable' );

	}

	public function add_metbox() {
		$screens = array( 'product' );
		add_meta_box( $this->plugin['prefix'] . '_section', esc_attr__( 'FAQ', 'woo-product-faq' ), array(
			$this,
			'metabox'
		), $screens );
	}

	public function metabox( $post, $meta ) {

		$post_id = $post->ID;
		$data    = get_post_meta( $post_id, '_' . $this->plugin['prefix'], false );

		if ( ! empty( $data[0] ) ) {
			$count = count( $data[0]['title'] );
		} else {
			$count = null;
		}

		wp_nonce_field( $this->plugin['prefix'] . '_action', $this->plugin['prefix'] . '_nonce_field' );
		if ( $count > 0 ) {
			for ( $i = 0; $i < $count; $i ++ ) {
				$ii = $i + 1; ?>

                <div class="faq-block">
                    <div class="faq-title"><?php esc_html_e( "FAQ", 'woo-product-faq' ); ?>:
                        <span class="faq-number"><?php echo esc_attr( $ii ); ?></span>
                        <span class="remove"><?php esc_html_e( "Remove", 'woo-product-faq' ); ?></span>
                    </div>
                    <input type="text" placeholder="<?php esc_attr_e( "Enter FAQ title", 'woo-product-faq' ); ?>"
                           name="<?php echo esc_attr( $this->plugin['prefix'] ); ?>[title][]"
                           value="<?php echo esc_attr( $data[0]['title'][ $i ] ); ?>">
                    <textarea placeholder="<?php esc_attr_e( "Enter FAQ description", 'woo-product-faq' ); ?>"
                              rows="5" name="<?php echo esc_attr( $this->plugin['prefix'] ); ?>[desc][]"><?php echo esc_attr( $data[0]['desc'][ $i ] ); ?></textarea>
                </div>
				<?php
			}
		}
		?>

        <div class="submit" id="faq-button" style="float: none; clear:both; background:#fff; padding: 4px 4px 0 0;">
            <button class="button-secondary" id="<?php echo esc_attr( $this->plugin['prefix'] ); ?>_button">
				<?php esc_attr_e( "Add New Faq", 'woo-product-faq' ); ?>
            </button>
        </div>
		<?php
	}

	public function save_metbox( $post_id ) {
		$prefix = $this->plugin['prefix'];

		if ( ! isset( $_POST[ $prefix ] ) ) {
			delete_post_meta( $post_id, '_' . $prefix );
			return;
		}

		if ( ! wp_verify_nonce( $_POST[ $prefix . '_nonce_field' ], $prefix . '_action' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$data = array();
		foreach ( $_POST[ $prefix ] as $key => $massive ) {
			foreach ( $massive as $mkey => $value ) {
				if ( $key == 'title' ) {
					$data[ $key ][ $mkey ] = sanitize_text_field( $value );
				} else {
					$data[ $key ][ $mkey ] = sanitize_textarea_field( $value );
				}
			}
		}
		update_post_meta( $post_id, '_' . $prefix, $data );

	}


}


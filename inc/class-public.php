<?php
/**
 * Public Class
 *
 * @package     Wow_Plugin
 * @subpackage  Public
 * @copyright   Copyright (c) 2018, Dmytro Lobov
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

namespace woo_product_faq;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WP_Plugin_Public {

	public function __construct( $info ) {
		$this->plugin = $info['plugin'];

		add_action( 'wp_enqueue_scripts', array( $this, 'include_scripts' ) );
		add_filter( 'woocommerce_product_tabs', array( $this, 'product_faq_tab' ) );
		add_shortcode( 'woo_product_faq', array( $this, 'shortcode' ) );
	}

	public function include_scripts() {
		$pre_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_style( $this->plugin['slug'] . '-front', $this->plugin['url'] . 'assets/style' . $pre_suffix . '.css' );

		wp_enqueue_script( $this->plugin['slug'] . '-front', $this->plugin['url'] . 'assets/script' . $pre_suffix . '.js', array( 'jquery' ), null, true );
	}

	public function product_faq_tab( $tabs ) {

		$post_id = get_the_ID();

		$faqs = get_post_meta( $post_id, '_' . $this->plugin['prefix'], false );

		if ( ! empty( $faqs[0] ) ) {

			$tabs['product_faq_tab'] = array(
				'title'    => esc_attr__( 'FAQ', 'woo-product-faq' ),
				'priority' => 50,
				'callback' => array( $this, 'faq_content' ),
			);
		}

		return $tabs;

	}

	public function faq_content() {

		global $post;

		if ( is_object( $post ) && $post->post_type != 'product' ) {
			return;
		}

		$post_id = $post->ID;

		$faqs = get_post_meta( $post_id, '_' . $this->plugin['prefix'], false );


		if ( ! empty( $faqs[0] ) ) {
			$count = count( $faqs[0]['title'] ); ?>

            <h2 class="woocommerce-Faq-title"><?php esc_attr_e( 'FAQ', 'woo-product-faq' ); ?></h2>

			<?php
			if ( $count > 0 ) {
				for ( $i = 0; $i < $count; $i ++ ) {
					; ?>

                    <div class="accordion-wrap">
                        <div class="accordion-block">
                            <div class="accordion-title">
                                <span class="plus">+</span>
                                <span class="minus">-</span>
                                <span class="faq-title"><?php echo esc_html( $faqs[0]['title'][ $i ] ); ?></span>
                            </div>
                            <div class="accordion-content"><?php echo wpautop( $faqs[0]['desc'][ $i ] ); ?></div>
                        </div>
                    </div>
					<?php
				}
			}
		}

	}

	public function shortcode( $atts ) {
		extract( shortcode_atts( array( 'id' => "" ), $atts ) );

		$post_id = $id;

		$faqs = get_post_meta( $post_id, '_' . $this->plugin['prefix'], false );

        $faqContent = '';
		if ( ! empty( $faqs[0] ) ) {
			$count = count( $faqs[0]['title'] );
			if ( $count > 0 ) {
				for ( $i = 0; $i < $count; $i ++ ) {

                    $faqContent .= '<div class="accordion-wrap">
                        <div class="accordion-block">
                            <div class="accordion-title">
                                <span class="plus">+</span>
                                <span class="minus">-</span>
                                <span class="faq-title">'. esc_html( $faqs[0]['title'][ $i ] ) . '</span>
                            </div>
                            <div class="accordion-content">' . wpautop( $faqs[0]['desc'][ $i ] ) . '</div>
                        </div>
                    </div>';

				}
			}
		}

        return $faqContent;


	}

}

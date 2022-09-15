<?php
use WC_Better_Shipping_Calculator_for_Brazil\Helpers as h;

$five_stars = '&#9733;&#9733;&#9733;&#9733;&#9733;';
$message = sprintf(
    /* translators: %1$s is replaced with plugin name and %2$s with 5 stars */
    esc_html__( 'Help us keep the %1$s plugin free and always up to date making a donation or rating %2$s on WordPress.org.', 'wc-better-shipping-calculator-for-brazil' ),
    '<strong>' . esc_html( h::config_get( 'NAME' ) ) . '</strong>',
    $five_stars
);
$id = esc_attr( h::prefix( 'notice_donation' ) );
?>

<div class="<?php echo esc_attr( $args['class'] ) ?>" id="<?php echo $id ?>">
    <p><?php echo $message; ?></p>
    <p>
        <a href="<?php echo esc_url( h::config_get( 'DONATION_URL' ) ) ?>" target="_blank" class="button button-primary"><?php esc_html_e( 'Donate', 'wc-better-shipping-calculator-for-brazil' ); ?></a>
        <a href="<?php echo esc_attr( h::config_get( 'PLUGIN_REVIEWS' ) ) ?>" target="_blank" class="button button-secondary"><?php esc_html_e( 'Make a review', 'wc-better-shipping-calculator-for-brazil' ); ?></a>
    </p>

    <button type="button" class="notice-dismiss" title="<?php esc_attr_e( 'Dismiss this notice.' ); ?>">
        <span class="screen-reader-text"><?php esc_html_e( 'Dismiss this notice.' ); ?></span>
    </button>
</div>

<script>
    window.addEventListener('DOMContentLoaded', () => {
        const notice = document.querySelector('#<?php echo $id ?> .notice-dismiss');
        notice.addEventListener('click', (evt) => {
            const cookie = '<?php echo esc_js( "{$args['cookie']}=yes;max-age={$args['cookie_max_age']};secure;samesite=strict" ) ?>';
            document.cookie = cookie;
            evt.currentTarget.parentNode.style.display = 'none';
            evt.currentTarget.parentNode.style.visibility = 'hidden';
        })
    })
</script>

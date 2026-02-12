<!DOCTYPE html>
<html <?php language_attributes(); ?> class="gsp2-dark-mode">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo esc_html(get_bloginfo('name')); ?> - Secure Global Payments</title>
    <?php wp_head(); ?>
</head>
<body <?php body_class('gsp2-theme gsp2-dark-mode'); ?>>

<?php include GSP2_PLUGIN_DIR . 'templates/partials/navigation.php'; ?>

<main class="gsp2-main">
    <?php include GSP2_PLUGIN_DIR . 'templates/partials/hero.php'; ?>
    <?php include GSP2_PLUGIN_DIR . 'templates/partials/trust.php'; ?>
    <?php include GSP2_PLUGIN_DIR . 'templates/partials/how-it-works.php'; ?>
    <?php include GSP2_PLUGIN_DIR . 'templates/partials/pay-online.php'; ?>
    <?php include GSP2_PLUGIN_DIR . 'templates/partials/transfer.php'; ?>
    <?php include GSP2_PLUGIN_DIR . 'templates/partials/crypto-table.php'; ?>
    <?php include GSP2_PLUGIN_DIR . 'templates/partials/contact.php'; ?>
</main>

<?php include GSP2_PLUGIN_DIR . 'templates/partials/footer.php'; ?>

<?php wp_footer(); ?>
</body>
</html>

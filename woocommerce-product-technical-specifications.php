<?php
/*
 * Plugin Name: WooCommerce Product Technical Specifications
 * Text Domain: woocommerce-product-technical-specifications
 * Domain Path: /languages
 */

register_activation_hook(__FILE__, 'wpts_activate');
register_deactivation_hook(__FILE__, 'wpts_deactivate');

function wpts_activate() {

}

function wpts_deactivate() {

}

add_action( 'init', 'wpts_load_textdomain' );
function wpts_load_textdomain() {
	load_plugin_textdomain( 'woocommerce-product-technical-specifications', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
}

add_action('add_meta_boxes', 'wpts_add_technical_specifications_box');
function wpts_add_technical_specifications_box() {
    add_meta_box(
        'wpts_technical_specifications_box',
        __('Technical Specifications', 'woocommerce-product-technical-specifications'),
        'wpts_display_technical_specifications_editor',
        'product'
    );
}

function wpts_display_technical_specifications_editor($post) {
    $meta = get_post_meta($post->ID, 'wpts_technical_specifications', true);
    echo '<input type="hidden" name="wpts_nonce" value="'.wp_create_nonce(basename(__FILE__)).'" />';
    echo '<table class="wpts-specification-table">';
    echo '<tbody>';
    echo '  <tr class="wpts-specification-template">';
    echo '      <td>';
    echo '          <span class="wpts-specification-drag">↕️</span>';
    echo '      </td>';
    echo '      <td>';
    echo '          <input type="text" value="">';
    echo '      </td>';
    echo '      <td>';
    echo '          <input type="text" value="">';
    echo '      </td>';
    echo '      <td>';
    printf('          <button class="button wpts-specification-delete">%s</button>', esc_html(__('Delete', 'woocommerce-product-technical-specifications')));
    echo '      </td>';
    echo '  </tr>';
    if (!empty($meta)) {
        $specifications = json_decode($meta, true);
        foreach ($specifications as $specification) {
            $name = $specification['name'];
            $value = $specification['value'];

            echo '  <tr class="wpts-specification">';
            echo '      <td>';
            echo '          <span class="wpts-specification-drag">↕️</span>';
            echo '      </td>';

            echo '      <td>';
            echo "          <input type=\"text\" name=\"wpts-specification-name[]\" value=\"{$name}\">";
            echo '      </td>';

            echo '      <td>';
            echo "          <input type=\"text\" name=\"wpts-specification-value[]\" value=\"{$value}\">";
            echo '      </td>';

            echo '      <td>';
            printf('          <button class="button wpts-specification-delete">%s</button>', esc_html(__('Delete', 'woocommerce-product-technical-specifications')));
            echo '      </td>';
            echo '  </tr>';
        }
    }
    echo '</tbody>';
    echo '<tfoot>';
    echo '  <td>';
    echo '  </td>';
    echo '  <td>';
    printf('    <button class="button wpts-specification-add-button">%s</button>', esc_html(__('Add a specification', 'woocommerce-product-technical-specifications')));
    echo '  </td>';
    echo '  <td>';
    echo '  </td>';
    echo '  <td>';
    echo '  </td>';
    echo '</tfoot>';
    echo '</table>';
}

add_action('save_post', 'wpts_save_technical_specifications');
function wpts_save_technical_specifications($post_id) {
    if (array_key_exists('wpts-specification-name', $_POST) &&
        array_key_exists('wpts-specification-value', $_POST)) {        
        $names = $_POST['wpts-specification-name'];
        $values = $_POST['wpts-specification-value'];

        $specifications = array();
        foreach ($names as $index => $name) {
            $specifications[] = [
                'name' => $name,
                'value' => $values[$index]
            ];
        }

        update_post_meta(
            $post_id,
            'wpts_technical_specifications',
            json_encode($specifications)
        );
    }
}

add_filter( 'woocommerce_product_tabs', 'wpts_add_technical_specification_tab');
function wpts_add_technical_specification_tab($tabs) {
    $tabs['wpts_tab'] = array(
        'title'     => __('Technical Specifications', 'woocommerce-product-technical-specifications'),
        'priority'  => 30,
        'callback'  => 'wpts_display_technical_specification_tab'
    );
    return $tabs;
}

function wpts_display_technical_specification_tab() {
    global $post;
    $meta = get_post_meta($post->ID, 'wpts_technical_specifications', true);
    if (!$meta) {
        echo __('This product has not been added technical specifications yet.', 'woocommerce-product-technical-specifications');
        return;
    }
    
    echo '<table class="wpts-technical-specification-table">';
    echo '    <thead>';
    echo '        <tr>';
    echo '            <th>';
    echo '                ' . esc_html(__('Specification', 'woocommerce-product-technical-specifications'));
    echo '            </th>';
    echo '            <th>';
    echo '                ' . esc_html(__('Value', 'woocommerce-product-technical-specifications'));
    echo '            </th>';
    echo '        </tr>';
    echo '    </thead>';
    echo '    <tbody>';
    $specifications = json_decode($meta, true);
    foreach ($specifications as $specification) {
        $name = esc_html($specification['name']);
        $value = esc_html($specification['value']);
        echo '        <tr>';
        echo "            <td>{$name}</td>";
        echo "            <td>{$value}</td>";
        echo '        </tr>';
    }
    echo '    </tbody>';
    echo '</table>';
}

add_action('admin_enqueue_scripts', 'wpts_add_admin_scripts');
function wpts_add_admin_scripts() {
    if (is_admin()) {
        wp_enqueue_script('wpts_repeatable_fields_js', plugin_dir_url(__FILE__) . '/admin/js/repeatable-fields.js', array('jquery'), false, true);
        wp_enqueue_style('wpts_repeatable_fields_css', plugin_dir_url(__FILE__) . '/admin/css/repeatable-fields.css');
    }
}

add_action('wp_enqueue_scripts', 'wpts_add_scripts');
function wpts_add_scripts() {
    wp_enqueue_style('wpts_technical_specifications_css', plugin_dir_url(__FILE__) . '/public/css/technical-specifications.css');
}

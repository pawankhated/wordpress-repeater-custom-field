<?php
include( get_theme_file_path( '/includes/admin_methods.php' ) );
 // Enqueue scripts and styles.

 function function_theme_enqueue_scripts() {
 function admin_enqueue($hook) {
  wp_register_style( 'admin_custom_style', get_stylesheet_directory_uri() . '/assets/admin/admin_style.css', false, '1.0.0' );
  wp_enqueue_style( 'admin_custom_style' );

  wp_register_style( 'admin_select_two', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/css/select2.min.css', false, '1.0.0' );
  wp_enqueue_style( 'admin_select_two' );

  wp_register_script('admin_select_two', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/js/select2.min.js', array('jquery'), '1.0.0',true);
  wp_enqueue_script( 'admin_select_two' );

  wp_register_script('admin_custom_script', get_stylesheet_directory_uri() . '/assets/admin/admin_script.js', array('jquery'), '1.1.0',true);
  wp_enqueue_script( 'admin_custom_script' );

}

add_action('admin_enqueue_scripts', 'admin_enqueue');
function Repeatable_meta_box_display() {
    global $post;
    $customdata_group = get_post_meta($post->ID, 'customdata_group', true);
    wp_nonce_field( 'gpm_repeatable_meta_box_nonce', 'gpm_repeatable_meta_box_nonce' ); ?>
     <div class="name_of_service name-service">
        <label>Name of the Service</label>
        <input type="text" placeholder="" title="Name of the Service" name="home_care_title" />
    </div>
      <div class="name_of_service">
        <label>Name of Sub Service</label>
    </div>
  <table id="repeatable-fieldset-one" width="100%" class="repeat_table table" cellspacing="0"><tbody>
    <?php
     if ( $customdata_group ) :
      foreach ( $customdata_group as $field ) { ?>
    <tr><td width="15%"><input type="text"  placeholder="Name of the Service" name="home_care_title" value="<?php if($field['home_care_title'] != '') echo esc_attr( $field['home_care_title'] ); ?>" /></td>

    </tr>  
    <tr>
      <td>
        <input type="checkbox" placeholder="Ceck Sub Category" name="home_care_sub_category__checkbox[]"/>
          <input type="text" placeholder="Sub Category" name="home_care_sub_category[]"/>
          <a class="button remove-row" href="#">Remove</a>
    </td>
    </tr>
    <?php
    }
    else :
    // show a blank one
    ?>
  <tr class="subservice_inner_row">
      <td><input type="checkbox" placeholder="Ceck Sub Category" name="home_care_sub_category__checkbox[]"/></td>
      <td><input type="text" placeholder="" name="home_care_sub_category[]"/></td>
      <td><a class="button remove-row" href="#">Remove</a></td>
         
     </div>
    </td>
    </tr>
    <?php endif; ?>

    <!-- empty hidden one for jQuery -->
    <tr class="empty-row screen-reader-text subservice_inner_row">
      <td><input type="checkbox" placeholder="Ceck Sub Category" name="home_care_sub_category__checkbox[]"/></td>
        <td><input type="text" placeholder="Sub Category" name="home_care_sub_category[]"/></td>
        <td><a class="button remove-row" href="#">Remove</a></td>
     </td>
    </tr>
  </tbody>
</table>
<p><a id="add-row" class="button" href="#">Add another</a></p>
 <?php
}


add_action('save_post', 'custom_repeatable_meta_box_save');
function custom_repeatable_meta_box_save($post_id) {
    if ( ! isset( $_POST['gpm_repeatable_meta_box_nonce'] ) ||
    ! wp_verify_nonce( $_POST['gpm_repeatable_meta_box_nonce'], 'gpm_repeatable_meta_box_nonce' ) )
        return;

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return;

    if (!current_user_can('edit_post', $post_id))
        return;

    $old = get_post_meta($post_id, 'customdata_group', true);
    $new = array();
    $invoiceItems = $_POST['home_care_title'];
    $prices = $_POST['home_care_sub_category'];
     $count = count( $invoiceItems );
     for ( $i = 0; $i < $count; $i++ ) {
        if ( $invoiceItems[$i] != '' ) :
            $new[$i]['home_care_title'] = stripslashes( strip_tags( $invoiceItems[$i] ) );
             $new[$i]['home_care_sub_category'] = stripslashes( $prices[$i] ); // and however you want to sanitize
        endif;
    }
    if ( !empty( $new ) && $new != $old )
        update_post_meta( $post_id, 'customdata_group', $new );
    elseif ( empty($new) && $old )
        delete_post_meta( $post_id, 'customdata_group', $old );


}
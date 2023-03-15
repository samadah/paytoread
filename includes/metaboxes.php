<?php

//Put global con outside
 global $wpdb; 
 
 function loopwi_p2r_custom_box_html( $post ) {
    $value = esc_html(get_post_meta( $post->ID, '_p2r_meta_key', true ));
    $p2r_value_currency = esc_html(get_post_meta( $post->ID, '_p2r_meta_key_currency', true));
    $value_amt = esc_html(get_post_meta( $post->ID, '_p2r_meta_key_amt', true ));
    ?> 
    <h3>Currency: <small>e.g. USD, Euro, NGN, GBP, etc</small></h3>
    <input type="text" value="<?php echo esc_html($p2r_value_currency); ?>" name="p2r_field_currency" id="p2r_field_currency" class="number postbox" required="1">

    <h3>Amount:</h3>
    <input type="number" value="<?php echo esc_html($value_amt); ?>" name="p2r_field_amt" id="p2r_field_amt" class="number postbox" required="1" min="0">
                     
            <input type="hidden" name="page_id" id="page_id" value="<?php echo get_the_id(); ?>">
            <input type="hidden" name="link" id="link" value="<?php echo get_permalink(); ?>">

            <h3>Shortcode:</h3>
            <input type="text" class="postbox" name="shortcode" id="shortcode" value="[premium_content id=<?php echo get_the_id(); ?>]">
 
 
    <?php

}

//Save the cureency
function loopwi_p2r_save_postdata_amount( $post_id ) {
    if ( array_key_exists( 'p2r_field_amt', $_POST ) ) {
        update_post_meta(
            $post_id,
            '_p2r_meta_key_amt',
            sanitize_text_field($_POST['p2r_field_amt'])
        );
    }
}
add_action( 'save_post', 'loopwi_p2r_save_postdata_amount' );

//Save the amount
function loopwi_p2r_save_postdata_currency( $post_id ) {
    if ( array_key_exists( 'p2r_field_currency', $_POST ) ) {
        update_post_meta(
            $post_id,
            '_p2r_meta_key_currency',
            sanitize_text_field($_POST['p2r_field_currency'])
        );
    }
}
add_action( 'save_post', 'loopwi_p2r_save_postdata_currency' );

//Activate plugin before all these come up
global $wpdb; 
$results = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}p2rplugins_settings WHERE pid = 1" );
    if ( null == $results->site_code ) {
//Do nothing
      }else{
        //get datas
    $site_code_p2r = $results->site_code; 
    $button_label_p2r = $results->button_label;
    $first_text_p2r = $results->first_text;
    $second_text_p2r = $results->second_text;

    //Declare variables
    global $site_code_p2r; 
    global $button_label_p2r; 
    global $first_text_p2r; 
    global $second_text_p2r; 

//payment function
               function loopwi_p2r_show_payment($id, $link, $currency, $value_amt) { 
                //
                global $site_code_p2r; 
                global $button_label_p2r; 
                global $first_text_p2r; 
                global $second_text_p2r; 

                //
                    //Encrypt
                $key = $site_code_p2r;
                $plaintext = $link;
                $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
                $iv = openssl_random_pseudo_bytes($ivlen);
                $ciphertext_raw = openssl_encrypt($plaintext, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
                $hmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
                $ciphertext = base64_encode( $iv.$hmac.$ciphertext_raw );

                //
                $payment_text = "<div class='p2r_donate_area'><h3>".esc_html($first_text_p2r)."</h3> <p>".esc_html($second_text_p2r)."</p><h3>".esc_html($currency)." ".esc_html($value_amt)."</h3><form method='POST' action='https://loopwi.com/api/p2r/get_link'><input type='hidden' name='site_code' value='".md5($site_code_p2r)."'><input type='hidden' name='id' value='".$ciphertext."'><input type='hidden' name='amount' value='".esc_html($value_amt)."'><button type='submit' class='p2r_btn'>".esc_html($button_label_p2r)."  &#8594; </button></form><br><img src='".plugin_dir_url( dirname( __FILE__ ) )."images/payment-methods.png' style='width:200px;' style='width:200px;'></div>";
 
                  return $payment_text;
                } 


//Create shortcod for this page
function loopwi_post__shortcode( $atts ) {
    $a = shortcode_atts(
        array (
            'id'   => false,
            'type' => "content",
        ), $atts );

    $id   = $a [ 'id' ];
    $type = $a [ 'type' ];

    // bad id
    if ( ! is_numeric( $id ) ) {
        return '';
    }

    // find the post
    $post = get_post( $id );

    // bad post
    if ( ! $post ) {
        return '';
    }

    // allow for other post attributes
    switch ( $type ) {

        case "content":
            return $id === get_the_ID() || $id === get_queried_object_id()
                ? '' // no recursive loops!
                //: apply_filters( 'the_content', $post->post_content );  
                : apply_filters( 'the_content', wp_trim_words($post->post_content, 200, ' ....... '.loopwi_p2r_show_payment($id, get_permalink($id), get_post_meta($id, '_p2r_meta_key_currency', true ), get_post_meta($id, '_p2r_meta_key_amt', true )).' ' ));

        case "title":
            return $post->post_title;
    }

    // nothing to see here
    return '';
}

//Add payment system 
add_shortcode( 'premium_content', 'loopwi_post__shortcode' );


// Check if it contains this shortcode
  function loopwi_p2r_wordpress_doc_head() {
    global $post;
    if ( has_shortcode( $post->post_content, 'premium_content' ) ) {

            //Remove filter
            remove_filter('the_content', 'show_donate'); 
 
    }
}
add_action( 'template_redirect', 'loopwi_p2r_wordpress_doc_head', 5 );
} //you must activate site before this shows
?>

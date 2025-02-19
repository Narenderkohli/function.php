<?php
/**
 * Recommended way to include parent theme styles.
 * (Please see http://codex.wordpress.org/Child_Themes#How_to_Create_a_Child_Theme)
 *
 */  

add_action( 'wp_enqueue_scripts', 'twenty_twenty_one_child_style' );
				function twenty_twenty_one_child_style() {
					wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
					wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array('parent-style') );
				}


     add_action( 'woocommerce_single_product_summary', 'shoptimizer_custom_heading_field', 3 );
  
			function shoptimizer_custom_heading_field() { ?>

			<?php if(get_field('custom_heading')) { ?>
				<div class="cg-author"><?php the_field('custom_heading'); ?></div>
			<?php
			  }
			}

	   ?>

<?php
	    add_action( 'woocommerce_single_product_summary', 'shoptimizer_custom_description_field', 35 );
  
			function shoptimizer_custom_description_field() { ?>

			<?php if(get_field('custom_description')) { ?>
				<div class="custom-description"><?php the_field('custom_description'); ?></div>
			<?php
			  }
			}


			add_action( 'woocommerce_single_product_summary', 'shoptimizer_custom_image_field', 30 );
                 function shoptimizer_custom_image_field() {
				    $custom_image = get_field('custom-image'); // Get the image field

				    if ($custom_image) {

                         ?>

				      <div class="custom-image">
				        <img src=" <?php echo $custom_image ?>" style="padding-top: 20px;">
				        </div>
				        <?php
				    }
				}
              ?>


<?php
add_action( 'woocommerce_single_product_summary', 'shoptimizer_custom_group_field', 30 );

function shoptimizer_custom_group_field() {
    $project_details = get_field('single-product-page-custom-field'); 

    if ($project_details) {
        ?>
        <h3><?php echo ($project_details['heading']); ?></h3>

        <div class="group-description">
            <?php echo ($project_details['description']); ?>
        </div>

        <?php
        if (!empty($project_details['image'])) {
            ?>
            <div class="custom-image">
                <img src="<?php echo ($project_details['image']); ?>" style="padding-top: 20px;">
            </div>
            <?php 
        }
    }
   }

  function wk_add_text_field() { ?>
        <div class="custom-field-wrap" style="margin: 10px;">
            <label for="custom-field">Name</label>
            <input type="text" name='custom-field' id='custom-field' value='' placeholder="enter name here">
        </div>
        <?php
    }
    add_action( 'woocommerce_before_add_to_cart_button', 'wk_add_text_field' );

        function wk_add_to_cart_validation( $passed, $product_id, $quantity, $variation_id = null ) {
            if ( empty( $_POST['custom-field'] ) ) {
                $passed = false;
                wc_add_notice( __( 'Quote is a required field.', 'webkul' ), 'error' );
            }
            return $passed;
        }
  add_filter( 'woocommerce_add_to_cart_validation', 'wk_add_to_cart_validation', 10, 4 );
    function wk_add_cart_item_data( $cart_item_data, $product_id, $variation_id ) {
        if ( isset( $_POST['custom-field'] ) ) {
            $cart_item_data['pr_field'] = sanitize_text_field( $_POST['custom-field'] );
        }
        return $cart_item_data;
    }
  add_filter( 'woocommerce_add_cart_item_data', 'wk_add_cart_item_data', 10, 3 );
       function wk_get_item_data( $item_data, $cart_item_data ) {
        if ( isset( $cart_item_data['pr_field'] ) ) {
            $item_data[] = array(
                'key'   => __( 'Custom Field Text', 'webkul' ),
                'value' => wc_clean( $cart_item_data['pr_field']),
            );
        }
        return $item_data;
    }
 add_filter( 'woocommerce_get_item_data', 'wk_get_item_data', 10, 2 );

    function wk_checkout_create_order_line_item( $item, $cart_item_key, $values, $order ) {
        if ( isset( $values['pr_field'] ) ) {
            $item->add_meta_data(
                __( 'Custom Field Text', 'webkul' ),
                $values['pr_field'],
                true
            );
        }
       }
  add_action( 'woocommerce_checkout_create_order_line_item', 'wk_checkout_create_order_line_item', 10, 4 );


    // single product image code start here
          add_action('woocommerce_before_add_to_cart_button', 'custom_image_upload_field');
            function custom_image_upload_field() {
                ?>
                <div class="custom-upload-field">
                    <label for="custom_image">Upload Image:</label>
                    <input type="file" name="custom_image" id="custom_image"  hidden accept="image/*">
                    <br>
                    <a id="merged-image-link" href="#" target="_blank" style="display: none;">uploaded image </a>
                </div>

                <script>
                document.getElementById("custom_image").addEventListener("change", function(event) {
                    var reader = new FileReader();
                    reader.onload = function() {
                        var preview = document.getElementById("custom_image_preview");
                        preview.src = reader.result;
                        preview.style.display = "block";
                    };
                    reader.readAsDataURL(event.target.files[0]);
                });
                </script>
                <?php
            }


   // image cart page code 
      add_filter('woocommerce_add_cart_item_data', 'save_custom_image_in_cart', 10, 2);
         function save_custom_image_in_cart($cart_item_data, $product_id) {
            if (!empty($_FILES['custom_image']['name'])) {
                $upload = wp_upload_bits($_FILES['custom_image']['name'], null, file_get_contents($_FILES['custom_image']['tmp_name']));
                if (!$upload['error']) {
                    $cart_item_data['custom_image'] = esc_url($upload['url']);
                }
            }
            return $cart_item_data;
        }

     add_filter('woocommerce_cart_item_name', 'display_custom_image_in_cart', 10, 3);
        function display_custom_image_in_cart($product_name, $cart_item, $cart_item_key) {
            if (isset($cart_item['custom_image'])) {
                $product_name .= '<p><strong>Uploaded Image:</strong><br><img src="' . esc_url($cart_item['custom_image']) . '" width="100"></p>';
            }
            return $product_name;
        }
   add_filter('woocommerce_checkout_cart_item_quantity', 'display_custom_image_in_checkout', 10, 2);
    function display_custom_image_in_checkout($quantity, $cart_item) {
        if (isset($cart_item['custom_image'])) {
            $quantity .= '<p><strong>Uploaded Image:</strong><br><img src="' . esc_url($cart_item['custom_image']) . '" width="100"></p>';
        }
        return $quantity;
    }

   add_action('woocommerce_checkout_create_order_line_item', 'save_custom_image_to_order', 10, 4);
    function save_custom_image_to_order($item, $cart_item_key, $values, $order) {
        if (isset($values['custom_image'])) {
            $item->add_meta_data('Uploaded Image', esc_url($values['custom_image']), true);
        }
    }
  add_filter('woocommerce_order_item_name', 'display_custom_image_in_admin_order', 10, 2);
        function display_custom_image_in_admin_order($item_name, $item) {
            if ($custom_image = $item->get_meta('Uploaded Image')) {
                $item_name .= '<p><strong>Uploaded Image:</strong><br><img src="' . esc_url($custom_image) . '" width="100"></p>';
            }
            return $item_name;
        }
     



     add_action('woocommerce_before_add_to_cart_button', 'multi_image_upload_single_page');

function multi_image_upload_single_page() {
    // Include your image uploader
    include('single-product-image-up.php');
}

   ?>


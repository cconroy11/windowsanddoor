<?php

/*

Custom code for gravity forms.
This is built specifically for the estimate form on WDS.


*/


//Populates post names into correct pricing field drop downs.
add_filter( 'gform_pre_render_2', 'populate_posts' );
add_filter( 'gform_pre_validation_2', 'populate_posts' );
add_filter( 'gform_pre_submission_filter_2', 'populate_posts' );
add_filter( 'gform_admin_pre_render_2', 'populate_posts' );
function populate_posts( $form ) {

	foreach ( $form['fields'] as &$field ) {


		if ( $field->type != 'product' || strpos( $field->cssClass, 'populate' ) === false ) {
			continue;
		}

		//Populate Door posts for any pricing drop down with and ID of 16 or higher
		if ( $field->id > 15 ) {


			$args  = array(
				'posts_per_page' => - 1,
				'post_type'      => 'door',
				'post_status'    => 'publish',
				'tax_query'      => array(
					array(
						'taxonomy' => 'door_type',
						'field'    => 'id',
						'terms'    => $field->conditionalLogic['rules'][0]['value'],
					),
				),
			);
			$posts = get_posts( $args );


			$choices = array();

			foreach ( $posts as $post ) {
				$dollar_signs = get_field( 'door_pricing', $post->ID );
				$pricing      = get_field( 'door_pricing_brackets', 'options' );
				$price_value  = 0;
				foreach ( $pricing as $price ) {

					if ( $price['dollar_signs'] == $dollar_signs ) {
						$price_value = $price['price'];

					}

				}
				$choices[] = array(
					'text'       => $post->post_title . " - " . $dollar_signs,
					'slug'       => '',
					'value'      => $post->ID,
					'isSelected' => '',
					'price'      => $price_value
				);
			}

			$field->placeholder = 'Select a Door';
			$field->choices     = $choices;

		} else {

			//Populate Window posts for any product drop down with and ID of 15 or lower
			$args  = array(
				'posts_per_page' => - 1,
				'post_type'      => 'window',
				'post_status'    => 'publish',
				'tax_query'      => array(
					array(
						'taxonomy' => 'window_style',
						'field'    => 'id',
						'terms'    => $field->conditionalLogic['rules'][0]['value'],
					),
				),
			);
			$posts = get_posts( $args );


			$choices = array();

			foreach ( $posts as $post ) {
				$dollar_signs = get_field( 'window_pricing', $post->ID );
				$pricing      = get_field( 'window_pricing_brackets', 'options' );
				$price_value  = 0;
				foreach ( $pricing as $price ) {

					if ( $price['dollar_signs'] == $dollar_signs ) {
						$price_value = $price['price'];

					}

				}
				$choices[] = array(
					'text'       => $post->post_title . " - " . $dollar_signs,
					'slug'       => '',
					'value'      => $post->ID,
					'isSelected' => '',
					'price'      => $price_value
				);
			}

			$field->placeholder = 'Select a Window';
			$field->choices     = $choices;

		}


	}

	return $form;
}

// Brings in custom tax ACF image field to the form for styling
add_filter( 'gform_field_content', 'render_form_edits', 10, 5 );
function render_form_edits( $content, $field, $value, $lead_id, $form_id ) {
	if ( $form_id != 2 ) {
		return $content;
	}

		//window_style ACF images
		if ( $field->id == 1 ) {

			$i = 1;
			foreach ( $field->choices as $choice ) {


				$term  = get_term( $choice['value'], 'window_style' );
				$image = get_field( 'image', $term );
				$size  = 'thumbnail';
				$thumb = $image['sizes'][ $size ];

				$content = str_replace( "<input name='input_1." . $i . "'", "<div class='product-image-option'><img class='estimate-img' src='" . $thumb . "' alt='" . $image['alt'] . "'><div class='selection-overlay'></div></div><input name='input_1." . $i . "'", $content );
				$i ++;

			}


		}

		//door_style ACF images
		if ( $field->id == 15 ) {

			$i = 1;
			foreach ( $field->choices as $choice ) {


				$term  = get_term( $choice['value'], 'door_type' );
				$image = get_field( 'image', $term );
				$size  = 'thumbnail';
				$thumb = $image['sizes'][ $size ];

				$content = str_replace( "<input name='input_15." . $i . "'", "<div class='product-image-option'><img class='estimate-img' src='" . $thumb . "' alt='" . $image['alt'] . "'><div class='selection-overlay'></div></div><input name='input_15." . $i . "'", $content );
				$i ++;

			}

		}

	if ($field->id == 36){
		$content = '<div class="estimate-total-container">' . $content . '</div>';
	}


		return $content;

}


//Add collapsible content containers to markup.  Only does this on form view not in admin.
if ( ! is_admin() ) {
	add_filter( 'gform_field_container_2', 'my_field_container', 10, 6 );
}
function my_field_container( $field_container, $field, $form, $css_class, $style, $field_content ) {

	//Windows collapsible content
	if ( $field->id == 1 ) {

		$field_container = '<li class="collapse-container window-styles-selection"><ul class="list-unstyled collapsible-list">
                <li class="collapsible-box">
                    <div class="collapsible-content ">
                    
                    <div class="collapsible-trigger">
                        <h4>Choose your windows</h4>
                        <div class="collapsible-controls">
        		            <div class="hide-show-btns">
        		                <em class="plus"><i class="fa fa-plus"></i></em>
        		                <em class="minus"><i class="fa fa-minus"></i></em>
        		            </div>
        		        </div>
        		     </div>
        		        
                    <div class="reveal-content"><ul>' . $field_container;
	}

	if ( $field->id == 14 ) {

		$field_container = $field_container . '
				</ul>
				</div>
                </div>
            </li></ul></li>';
	}

	//Door collapsible content
	if ( $field->id == 15 ) {
		$field_container = '<li class="collapse-container door-types-selection"><ul class="list-unstyled collapsible-list">
                <li class="collapsible-box">
                    <div class="collapsible-content">
                     <div class="collapsible-trigger">
                        <h4>Choose your doors</h4>
                        <div class="collapsible-controls">
        		            <div class="hide-show-btns">
        		                <em class="plus"><i class="fa fa-plus"></i></em>
        		                <em class="minus"><i class="fa fa-minus"></i></em>
        		            </div>
        		        </div>
        		     </div>
                    <div class="reveal-content"><ul>' . $field_container;
	}
	if ( $field->id == 22 ) {

		$field_container = $field_container . '
				</ul>
				</div>
                </div>
            </li></ul></li>';
	}

	return $field_container;

}

//Rename Term IDs to Option Label on Entry View.
add_filter( 'gform_entry_field_value', 'change_post_info', 10, 4 );
function change_post_info( $value, $field, $entry, $form ) {

	if ( $form['id'] != 2 ) {
		return $value;
	}

	$fields = [1, 2, 4, 6, 8, 10, 12, 14, 15,16,18, 20];

	//window_style labels
	if ( in_array($field->id , $fields)  ) {

		$value = $field->get_value_entry_detail( RGFormsModel::get_lead_field_value( $entry, $field ), '', true, 'text' );

	}


	return $value;

}


add_filter( 'gform_product_info', function ( $product_info, $form, $entry ) {

	foreach ( $product_info['products'] as $key => &$product ) {
		$field = GFFormsModel::get_field( $form, $key );
		if ( is_object( $field ) ) {

			if (is_numeric($product['name'])){
				$product['name'] = get_the_title($product['name']);
			}

		}
	}

	return $product_info;
}, 10, 3 );
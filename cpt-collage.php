<?php

function create_member_collage($post_id) {
	$args = array (
		'post_type'              => 'team-member',
		'post_status'            => array( 'publish' ),
		'orderby'                => 'menu_order'
	);

	$post_type = get_post_type($post_id);

	if ( "team-member" != $post_type ) return;

	$members = new WP_Query( $args );

	if ( $members->have_posts() ) {
		$images = array();
		while ( $members->have_posts() ) {
			$members->the_post();
			if(has_post_thumbnail()) {
				$imgsrc = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), "Full");
				$images[] = $imgsrc[0];
			}
		}
	}

	wp_reset_postdata();

	$num_columns = 10;
	$num_rows = ceil(sizeof($images) / $num_columns);

	if(sizeof($images) % $num_columns  !== 0) {
			do {
			$filler_images = $num_columns - (sizeof($images) % $num_columns);
			for($x = 0; $x < sizeof($images); $x++) {
				if(sizeof($images) % $num_columns  == 0) break;
				$images[] = $images[$x];
			}
		} while (sizeof($images) % $num_columns  !== 0);
	}

	$stack = new Imagick();
	$stack->setCompressionQuality(62);
	foreach($images as $image) {
		$stack->addImage(new Imagick($image));
	}


	$montage = $stack->montageImage(new ImagickDraw(), $num_columns . 'x' . $num_rows, '', 0, '0');
	$member_image = get_template_directory() . '/css/img/bg_headshots.jpg';
	$montage->writeImage($member_image);

}

add_action( 'save_post', 'create_member_collage' );
add_action( 'delete_post', 'create_member_collage' );
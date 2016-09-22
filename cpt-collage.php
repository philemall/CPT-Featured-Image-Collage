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
	$occupied_rows = ceil(sizeof($images) / $num_columns);
	$num_rows = 10;
	$height = '110';
	$width = '110';

	if(sizeof($images) % $num_columns  !== 0) {
		do {
			$filler_images = $num_columns - (sizeof($images) % $num_columns);
			for($x = 0; $x < sizeof($images); $x++) {
				if(sizeof($images) % $num_columns  == 0) break;
				$images[] = $images[$x];
			}
		} while (sizeof($images) % $num_columns  !== 0);
	}

	$image_list = $images;
	$shuffled = $images;
	for($row = 0; $row < ($num_rows - $occupied_rows); $row++) {
		shuffle($shuffled);
		$image_list = array_merge($image_list, $shuffled);
	}

	$stack = new Imagick();
	$stack->setCompressionQuality(62);
	foreach($image_list as $image) {
		$stack->addImage(new Imagick($image));
	}


	$montage = $stack->montageImage(new ImagickDraw(), $num_columns . 'x' . $num_rows, $height . 'x' . $width, 0, '0');
	$member_image = get_template_directory() . '/css/img/bg_headshots.jpg';
	$montage->writeImage($member_image);

}

add_action( 'save_post', 'create_member_collage' );
add_action( 'delete_post', 'create_member_collage' );
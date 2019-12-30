<?php
/*
    ===============================
    Custom Meta Boxes
    ===============================
*/

// function wpbook1_register_meta_boxes() {
//     add_meta_box( 'wpbook1-page-id', 'Meta Post', 'wpbook1_pages_function', 'post', 'Normal', 'high');
// }
// add_action('add_meta_boxes', 'wpbook1_register_meta_boxes');

function wpbook1_add_custom_box()
{
	$screens = array('book');
	foreach ($screens as $screen) {
        add_meta_box(
            'wpbook1_box_id',           // Unique ID
            __( 'Custom Meta Box', 'sitepoint' ),  	// Box title
			'wpbook1_custom_box_html1', // Content callback, must be of type callable
			$screen	     // Post type
		);
	}
}
add_action('add_meta_boxes_book', 'wpbook1_add_custom_box');


//callback function (metabox) for custom-post-type 'book'
function wpbook1_custom_box_html1($post)
{
	wp_nonce_field(basename(__FILE__), "wpbook1_cpt_nonce");
    //$value = get_post_meta($post->ID, '_wpbook1_meta_key', true);
    ?>
  	<form>
    	<label for = "textAuthorName">Author Name :</label>
		<?php $author_name = get_post_meta( $post->ID, 'book_author_name', true ) ?>
		<input type = "text" name = "textAuthorName" value = "<?php echo $author_name ?>" placeholder = "write author name">
		</br></br>
		
		<label for = "textPublisherName">Publisher Name :</label>
		<?php $publisher_name = get_post_meta( $post->ID, 'book_publisher_name', true ) ?>
		<input type = "text" name = "textPublisherName" value = "<?php echo $publisher_name ?>" placeholder = "write publisher name">
		</br></br>
		
		<label for = "textEdition">Edition :</label>
		<?php $edition_name = get_post_meta( $post->ID, 'book_editor_name', true ) ?>
		<input type = "text" name = "textEdition" value = "<?php echo $edition_name ?>" placeholder = "Edition">
		</br></br>
		
		<label for = "date">Date :</label>
		<?php $date = get_post_meta( $post->ID, 'book_date', true ) ?>
		<input type = "date" name = "date" value = "<?php echo $date ?>" placeholder = "Date">
		</br></br>
		
		<label for = "URL">URL :</label>
		<?php $url = get_post_meta( $post->ID, 'book_url', true ) ?>
		<input type = "url" name = "URL" value = "<?php echo $url ?>" placeholder = "URL">
		</br></br>
		
		<label for = "price">Price :</label>
		<?php $price = get_post_meta( $post->ID, 'book_price', true ) ?>
		<input type = "text" name = "price" value= "<?php echo $price ?>" placeholder = "Enter price in <?php echo $_SESSION['currency']; ?>">

	</form>
	<?php
}


function wpbook1_save_bookdata($post_id, $post)
{
	//veified nonce
	if( !isset($_POST['wpbook1_cpt_nonce']) || !wp_verify_nonce($_POST['wpbook1_cpt_nonce'], basename(__FILE__)) ){
		return $post_id;
	}

	//verifying slug value
	$post_slug = 'book';
	if( $post_slug != $post->post_type ) {
		return;
	}

	//save publisher value to database field
	$pub_name = '';
	if ( isset( $_POST['textPublisherName'] )){
		$pub_name = sanitize_text_field( $_POST['textPublisherName'] );
	}

	//save author value to database field
	$auth_name = '';
	if ( isset( $_POST['textAuthorName'] )){
		$auth_name = sanitize_text_field( $_POST['textAuthorName'] );
	}

	//save edition value to database field
	$editi = '';
	if ( isset( $_POST['textEdition'] )){
		$editi = sanitize_text_field( $_POST['textEdition'] );
	}
	//save date & time value to database field
	$date_time = '';
	if ( isset( $_POST['date'] )){
		$date_time = sanitize_text_field( $_POST['date'] );
	}
	
	//save url value to database field
	$URL = '';
	if ( isset( $_POST['URL'] )){
		$URL = sanitize_text_field( $_POST['URL'] );
	}

	//save price value to database field
	$price_1 = '';
	if ( isset( $_POST['price'] )){
		$price_1 = sanitize_text_field( $_POST['price'] );
	}

	update_post_meta($post_id,'book_publisher_name',$pub_name);
	update_post_meta($post_id,'book_author_name',$auth_name);
	update_post_meta($post_id,'book_editor_name',$editi);
	update_post_meta($post_id,'book_url',$URL);
	update_post_meta($post_id,'book_date',$date_time);
	update_post_meta($post_id,'book_price',$price_1);
}
add_action('save_post', 'wpbook1_save_bookdata', 10, 2);



//author metabox
function wpbook1_author_custom_box()
{
	$screens = array('book');
	foreach ($screens as $screen) {
        add_meta_box(
            'wpbook1_author_id',           // Unique ID
            __( 'author Meta Box', 'sitepoint' ),  	// Box title
			'wpbook1_author_box_html', // Content callback, must be of type callable
			$screen	     // Post type
		);
	}
}
add_action('add_meta_boxes_book', 'wpbook1_author_custom_box');


function wpbook1_author_box_html($post)
{
	wp_nonce_field(basename(__FILE__), "wpbook1_author_nonce");
    ?>
	<label for="dropDownAuthor">Author Name</label> 
	<select name="dropDownAuthor" id="dropDownAuthor">

	<?php
		$post_id = $post->ID;
		$author_id = get_post_meta($post_id, 'book_author_name', true);

		$all_authors = get_users(array('role' => 'author'));
		foreach($all_authors as $index => $author){
			$selected = "";
			if ( $author_id == $author->data->ID ){
				$selected = 'selected="selected"';
			}
			?>
				<option value = "<?php echo $author->data->display_name; ?>" <?php echo $selected ?>;  ><?php echo $author->data->display_name ?></option>
			<?php
		}
	?>

     </select>
   <?php
}

function wpbook1_save_author($post_id, $post)
{
	//veified nonce
	if( !isset($_POST['wpbook1_author_nonce']) || !wp_verify_nonce($_POST['wpbook1_author_nonce'], basename(__FILE__)) ){
		return $post_id;
	}

	//verifying slug value
	$post_slug = 'book';
	if( $post_slug != $post->post_type ) {
		return;
	}

	//save value to database field
	$author_name = '';
	if ( isset( $_POST['dropDownAuthor'] )){
		$author_name = sanitize_text_field( $_POST['dropDownAuthor'] );
	} else{
		$author_name = '';
	}

	update_post_meta(
		$post_id,
		'book_author_name',
		$author_name
	);
}
add_action('save_post', 'wpbook1_save_author', 10, 2);


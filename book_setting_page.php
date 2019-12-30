<?php

function wpbook_render_text($attributes) {
    $select = esc_attr(get_option('wpbook_second_section', ''));
    
    if ( $select == "USD" ){
        echo '<h5>' . $select .':' . $attributes['price'] / 71 .'$ </h5>';
	}
	else{
		echo '<h5> INR: ' . $attributes['price'] . 'rs. </h5>';
	}
?>
 
<?php
}
add_filter('pre_get_posts', 'number_of_posts_on_archive');
function number_of_posts_on_archive($query)
{
	$first_text = esc_attr(get_option('wpbook_first_text', ''));
    if(is_post_type_archive(array('book')))
    {
        $query->set('posts_per_page', $first_text);
    }
    return $query;
}

add_shortcode('book-settings', 'wpbook_render_text');
function wpbook_settings_page() {
	add_submenu_page(
		'edit.php?post_type=book', // top level menu page
		'Settings Page', // title of the settings page
		'Settings', // title of the submenu
		'manage_options', // capability of the user to see this page
		'wpbook-settings-page', // slug of the settings page
		'wpbook_settings_page_html' // callback function to be called when rendering the page
	);
	add_action('admin_init', 'wpbook_settings_init');
}

add_action('admin_menu', 'wpbook_settings_page');
function wpbook_settings_init() {
	add_settings_section(
		'first-settings-section', // id of the section
		'Post per page', // title to be displayed
		'', // callback function to be called when opening section
		'wpbook-settings-page' // page on which to display the section, this should be the same as the slug used in add_submenu_page()
    );
    	// register the setting
	register_setting(
		'wpbook-settings-page', // option group
		'wpbook_first_text'
    );

    add_settings_field(
		'wpbook-first-text', // id of the settings field
		'My First Text', // title
		'wpbook_settings_cb', // callback function
		'wpbook-settings-page', // page on which settings display
		'first-settings-section' // section on which to show settings
    );

    add_settings_section(
		'second-settings-section', // id of the section
        'Change to USD Currency', // title to be displayed
		'', // callback function to be called when opening section
		'wpbook-settings-page' // page on which to display the section, this should be the same as the slug used in add_submenu_page()
	);
    
    register_setting(
		'wpbook-settings-page', // option group
		'wpbook_second_section'
	);

    add_settings_field(
		'wpbook-second-section', // id of the settings field
		'Write USD to convert price in USD', // title
		'wpbook_settings_cb1', // callback function
		'wpbook-settings-page', // page on which settings display
		'second-settings-section' // section on which to show settings
	);
}


function wpbook_settings_cb() {
    $first_text = esc_attr(get_option('wpbook_first_text', ''));
  
	?>

    <div id="titlediv">
        <input id="title" type="text" name="wpbook_first_text" value="<?php echo $first_text; ?>">        
    </div>

    <?php
}

function wpbook_settings_cb1() {
    $select = esc_attr(get_option('wpbook_second_section', ''));    
    ?>

    <div id="titlediv1">
        <input id="title1" type="text" name="wpbook_second_section" value="<?php echo $select; ?>">        
    </div>

    <?php
}

function wpbook_settings_page_html() {
	// check user capabilities
	if (!current_user_can('manage_options')) {
		return;
	}
	?>

    <div class="wrap">
        <?php settings_errors();?>
        <form method="POST" action="options.php">
		    <?php settings_fields('wpbook-settings-page');?>
		    <?php do_settings_sections('wpbook-settings-page')?>
		    <?php submit_button();?>
        </form>
    </div>
    <?php
}



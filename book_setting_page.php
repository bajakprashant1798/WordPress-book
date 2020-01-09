<?php
/**
 * WordPress book plugin render text function
 * 
 * @param $attributes
 *  
 * @return int
 */
function WPbook_Render_text($attributes) 
{
    $select = esc_attr(get_option('wpbook_second_section', ''));
    if ($select == "USD") {
        echo '<h5>' . $select .':' . $attributes['price'] / 71 .'$ </h5>';
    } else {
        echo '<h5> INR: ' . $attributes['price'] . 'rs. </h5>';
    } ?>
            <?php
}
add_shortcode('book-settings', 'WPbook_Render_text');


/**
 * Number of posts per page function
 * 
 * @param $query
 * 
 * @return string
 */
function WPbook_Number_Of_Posts_On_Archive($query)
{
    $first_text = esc_attr(get_option('wpbook_first_text', ''));
    if (is_post_type_archive(array('book'))) {
        $query->set('posts_per_page', $first_text);
    }
    return $query;
}
add_filter('pre_get_posts', 'WPbook_Number_Of_Posts_On_Archive');

/**
 * Setting page function
 */
function WPbook_Settings_page() 
{
    add_submenu_page(
        'edit.php?post_type=book',  // top level menu page
        'Settings Page', // title of the settings page
        'Settings', // title of the submenu
        'manage_options', // capability of the user to see this page
        'wpbook-settings-page', // slug of the settings page
        'WPbook_Settings_Page_html' // callback function to be called when rendering the page
    );
    add_action('admin_init', 'WPbook_Settings_init');
}
add_action('admin_menu', 'WPbook_Settings_page');

/**
 * Setting init function
 */
function WPbook_Settings_init() 
{
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
        'WPbook_Settings_cb', // callback function
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
        'WPbook_Settings_cb1', // callback function
        'wpbook-settings-page', // page on which settings display
        'second-settings-section' // section on which to show settings
    );
}

/**
 * Settings callback function
 */
function WPbook_Settings_cb()
{
    $first_text = esc_attr(get_option('wpbook_first_text', '')); ?>

    <div id="titlediv">
        <input id="title" type="text" name="wpbook_first_text" value="<?php echo $first_text; ?>">        
    </div>
    <?php
}

/**
 * Settings callback function 1
 */
function WPbook_Settings_cb1() 
{
    $select = esc_attr(get_option('wpbook_second_section', ''));    
    ?>

    <div id="titlediv1">
        <input id="title1" type="text" name="wpbook_second_section" value="<?php echo $select; ?>">        
    </div>
    <?php
}

/**
 * Settings page html
 */
function WPbook_Settings_Page_html() 
{
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



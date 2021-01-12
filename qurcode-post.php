<?php

/*
Plugin Name: Post Qurcode
Plugin URI: https://dfinesoft.me
Description: Show post qurcode
Version: 1.0
Author: Sazin
Author URI: https://sazin.me
License: GPLv2 or later
Text Domain: post-qurcode
Domain Path: /languages/
*/


// function wordcount_activation_hook(){}
// register_activation_hook(__FILE__,"wordcount_activation_hook");

// function wordcount_deactivation_hook(){}
// register_deactivation_hook(__FILE__,"wordcount_deactivation_hook");





// plugin text doamin  loaded 

function post_qurcode_load_textdomain()
{
    load_plugin_textdomain('post-qurcode', false, dirname(__FILE__) . "/languages");
}
add_action("plugins_loaded", 'post_qurcode_load_textdomain');




/*--------------------------------------------------
	          out function Country list
---------------------------------------------------*/

$pqrc_countries = array(
    __('Afganistan', 'post-qurcode'),
    __('Bangladesh', 'post-qurcode'),
    __('Bhutan', 'post-qurcode'),
    __('India', 'post-qurcode'),
    __('Maldives', 'post-qurcode'),
    __('Nepal', 'post-qurcode'),
    __('Pakistan', 'post-qurcode'),
    __('Sri Lanka', 'post-qurcode'),
);

function pqrc_init()
{
    global $pqrc_countries;
    $pqrc_countries = apply_filters('pqrc_countries', $pqrc_countries);
}

add_action("init", 'pqrc_init');



// Output Nmae sunction 

$pqrc_namelist = array(
    __('Sazin', 'post-qurcode'),
    __('Sumyiya', 'post-qurcode'),
    __('Sagur', 'post-qurcode'),
    __('saifan', 'post-qurcode'),
    __('Ronok', 'post-qurcode'),
    __('Ifnan', 'post-qurcode'),
);

function pqrc_admininput()
{
    global $pqrc_namelist;
    $pqrc_addname = apply_filters('pqrc_addname', $pqrc_namelist);
}
add_action('init', 'pqrc_admininput');



/*--------------------------------------------------
	           QRC Function
---------------------------------------------------*/
function post_qurcode_function($content)
{

    $current_post_id = get_the_ID();
    $current_post_title = get_the_title($current_post_id);
    $current_post_url = urlencode(get_the_permalink($current_post_id));
    $current_post_type = get_post_type($current_post_id);
    $excluded_post = apply_filters('postc_exlcuded_post_types', array());
    if (in_array($current_post_type,  $excluded_post)) {
        return $content;
    }



    /*--------------------------------------------------
	           Show the Data on fronted 
---------------------------------------------------*/

    $height = get_option('pqurcode_height');
    $width = get_option('pqurcode_width');
    $extra_filed = get_option('pqurcode_extra');
    $pqrs_name = get_option('pqrc_slectname');

    $height = $height ? $height : 180;
    $width = $width ? $width : 180;
    $extra_filed = $extra_filed ? $extra_filed : "This Is Title";
    $pqrs_name = $pqrs_name ? $pqrs_name : "No Name Selected";



    $images_size = apply_filters(crsimg_size, "{$width}x{$height}");

    $extrafiled = sprintf("<h3> %s %s </h3>", $extra_filed, $pqrs_name);
    $image_src = sprintf('https://api.qrserver.com/v1/create-qr-code/?size=%s&data=%s', $images_size, $current_post_url);
    $content .= sprintf("<div class=''> %s <img src='%s' alt ='%s' /> </div>", $extrafiled, $image_src, $current_post_title);

    return $content;
}

add_filter('the_content', 'post_qurcode_function');




// Add admin setting fileds 

function pqurcode_swttings_init()
{

    /*--------------------------------------------------
	     Add settings filed on wp admin 
---------------------------------------------------*/
    add_settings_section('pqrc_section', __('Post to QRC', 'post-qurcode'), 'pqrc_section_title_cal', 'general');
    add_settings_field('pqurcode_height', __('QRQ Image Height', 'post-qurcode'), 'pqurc_display_height', 'general', 'pqrc_section');
    add_settings_field('pqurcode_width', __('QRQ Image width', 'post-qurcode'), 'pqurc_display_width', 'general', 'pqrc_section');
    add_settings_field('pqurcode_extra', __('QRC Exrta Fileds', 'qurcode-extra'), 'pqurc_display_extra', 'general', 'pqrc_section');
    add_settings_field('pqrc_select', __('Dropdown', 'post-qurcode'), 'pqrc_display_select_field', 'general', 'pqrc_section');
    add_settings_field('pqrc_slectname', __('Select Name', 'post-qurcode'), 'pqrc_select_thename', 'general', 'pqrc_section');
    add_settings_field('pqrc_checkbox', __('Select Country', 'post-qurcode'), 'pqrc_checkbox_dispaly', 'general', 'pqrc_section');
    add_settings_field('pqrc_toggle', __('Toggle Field', 'post-qurcode'), 'pqrc_display_toggle_field', 'general', 'pqrc_section');

    /*--------------------------------------------------
	     Register settings filed on wp admin 

---------------------------------------------------*/
    register_setting('general', 'pqurcode_height', array('sanitize_callback' => 'esc_attr'));
    register_setting('general', 'pqurcode_width', array('sanitize_callback' => 'esc_attr'));
    register_setting('general', 'pqurcode_extra', array('sanitize_callback' => 'esc_attr'));
    register_setting('general', 'pqrc_section', array('sanitize_callback' => 'esc_attr'));
    register_setting('general', 'pqrc_select', array('sanitize_callback' => 'esc_attr'));
    register_setting('general', 'pqrc_slectname', array('sanitize_callback' => 'esc_attr'));
    register_setting('general', 'pqrc_checkbox');
    register_setting('general', 'pqrc_toggle');


    /*--------------------------------------------------
	     Dispaly the input feileds on wp admin  
---------------------------------------------------*/

    // Select Country list show drowndown 

    function pqrc_display_select_field()
    {
        global $pqrc_countries;
        $option = get_option('pqrc_select');

        printf('<select id="%s" name="%s">', 'pqrc_select', 'pqrc_select');
        foreach ($pqrc_countries as $country) {
            $selected = '';
            if ($option == $country) {
                $selected = 'selected';
            }
            printf('<option value="%s" %s>%s</option>', $country, $selected, $country);
        }
        echo "</select>";
    }


    //  name drowndown 
    function pqrc_select_thename()
    {
        global $pqrc_namelist;
        $option = get_option('pqrc_slectname');

        printf('<select id="%s" name="%s">', 'pqrc_slectname', 'pqrc_slectname');
        foreach ($pqrc_namelist as $namelist) {
            $slelected = '';
            if ($option == $namelist) {
                $slelected = 'selected';
            }
            printf('<option value="%s" %s> %s </option>', $namelist, $slelected, $namelist);
        }

        echo '</select>';
    }


    //  Check box display
    function pqrc_checkbox_dispaly()
    {

        global $pqrc_namelist;
        $option = get_option('pqrc_checkbox');

        foreach ($pqrc_namelist as $country) {

            $selected = "";

            if (is_array($option)  && in_array($country, $option)) {
                $selected = 'checked';
            }

            printf('<input type="checkbox" name="pqrc_checkbox[]" value="%s" %s /> %s <br/>', $country, $selected, $country);
        }
    }

    // Show toggle  
    function pqrc_display_toggle_field()
    {
        $option = get_option('pqrc_toggle');
        echo '<div id="toggle1"></div>';
        echo "<input type='hidden' name='pqrc_toggle' id='pqrc_toggle' value='" . $option . "'/>";
    }

    // Disply the section title 
    function pqrc_section_title_cal()
    {
        echo "<h2>" .  __('Post QRC Settings Section', 'post-qurcode')  . "</h2>";
    }

    // Display the admin fileds Height
    function pqurc_display_height()
    {
        $height = get_option('pqurcode_height');
        printf("<input type='text' id='%s' name='%s' value='%s' />", 'pqurcode_height', 'pqurcode_height', $height);
    }

    // Display the admin fileds Height
    function pqurc_display_width()
    {
        $width = get_option('pqurcode_width');
        printf("<input type='text' id='%s' name='%s' value='%s' />", 'pqurcode_width', 'pqurcode_width', $width);
    }
    // Display Extra filed admin area 
    function pqurc_display_extra()
    {
        $extra_fi = get_option('pqurcode_extra');
        printf("<input type='text' id='%s' name='%s' value='%s' />", 'pqurcode_extra', 'pqurcode_extra', $extra_fi);
    }

    function pqurc_select_contry()
    {
        $country_select = get_option('country_select');
    }
}
add_action('admin_init', 'pqurcode_swttings_init');



/*--------------------------------------------------
	     Dispaly the input feileds on wp admin  
---------------------------------------------------*/


function pqrc_assets($screen)
{
    if ('options-general.php' == $screen) {
        wp_enqueue_style('pqrc-minitoggle-css', plugin_dir_url(__FILE__) . "/assets/css/minitoggle.css");
        wp_enqueue_script('pqrc-minitoggle-js', plugin_dir_url(__FILE__) . "/assets/js/minitoggle.js", array('jquery'), "1.0", true);
        wp_enqueue_script('pqrc-main-js', plugin_dir_url(__FILE__) . "/assets/js/pqrc-main.js", array('jquery'), time(), true);
    }
}
add_action('admin_enqueue_scripts', 'pqrc_assets');

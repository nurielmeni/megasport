<?php
include_once 'nlsFlow.php';
include_once 'customizerNiloosSetings.php';
include_once 'customizerNiloosOptions.php';

const NLS_FLOW_ELEMENTS = 3;

//include_once 'headerLogo.php';

/**
 * Customize the theme customizer
 */
add_action('customize_register', 'niloos_customizer_additions');

function niloos_customizer_additions($wp_customize)
{

  // Add the new Flow Panel
  $panel = $wp_customize->add_panel(
    'niloos_theme_options',
    array(
      //'priority'       => 100,
      'title'            => __('Niloos', 'NlsHunter'),
      'description'      => __('Niloos Module Settings', 'NlsHunter'),
    )
  );

  // Adds the settings - customizerNiloosSetings.php
  add_niloos_settings_section($wp_customize, $panel->id);

  // Adds the settings - customizerNiloosSetings.php
  add_niloos_options_section($wp_customize, $panel->id);

  // Adds the flow element and the shortcode (NlsHunter_flow), - nlsFlow.php
  add_flow_elements_general_section($wp_customize, $panel->id);

  /**
   * Add the Flow elements sections
   */
  for ($i = 1; $i <= NLS_FLOW_ELEMENTS; $i++) {
    add_flow_element_item_section($wp_customize, $panel->id, $i);
  }
}


function our_sanitize_function($input)
{
  return wp_kses_post(force_balance_tags($input));
}

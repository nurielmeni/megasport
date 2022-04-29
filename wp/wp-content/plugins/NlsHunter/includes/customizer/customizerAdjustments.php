<?php
include_once NLS__PLUGIN_PATH . '/includes/Hunter/NlsConfig.php';
include_once 'nlsFlow.php';
include_once 'customizerNiloosSetings.php';
include_once 'customizerNiloosOptions.php';
include_once 'customizerNiloosApplication.php';
include_once 'customizerNiloosAuth.php';

/**
 * Customize the theme customizer
 */
add_action('customize_register', 'niloos_customizer_additions');

function niloos_customizer_additions($wp_customize)
{

  // Add the new Niloos Panel
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

  // Adds the options - customizerNiloosSetings.php
  add_niloos_options_section($wp_customize, $panel->id);

  // Adds the application - customizerNiloosSetings.php
  add_niloos_application_section($wp_customize, $panel->id);

  // Adds the auth - customizerNiloosSetings.php
  add_niloos_auth_section($wp_customize, $panel->id);


  // Add the new Flow Panel
  $flowPanel = $wp_customize->add_panel(
    'flow_ekements_settings',
    array(
      //'priority'       => 100,
      'title'            => __('Flow Widget', 'NlsHunter'),
      'description'      => __('Flow elemnets settings', 'NlsHunter'),
    )
  );

  // Adds the flow element and the shortcode (NlsHunter_flow), - nlsFlow.php
  add_flow_elements_general_section($wp_customize, $flowPanel->id);

  /**
   * Add the Flow elements sections
   */
  for ($i = 1; $i <= NlsConfig::NLS_FLOW_ELEMENTS; $i++) {
    add_flow_element_item_section($wp_customize, $flowPanel->id, $i);
  }
}


function our_sanitize_function($input)
{
  return wp_kses_post(force_balance_tags($input));
}

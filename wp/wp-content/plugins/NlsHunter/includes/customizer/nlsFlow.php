<?php

/**
 * Returns a custom logo, linked to home unless the theme supports removing the link on the home page.
 *
 * @return string Custom logo markup.
 */

// Customizer settings
function add_flow_element_item_section($wp_customize, $panel, $index)
{
  /**
   * Add the new Flow element section
   */
  $section = $wp_customize->add_section('nls_flow_element_' . $index, [
    'title' => __('Flow Element - ', 'NlsHunter') . $index,
    'description' => __('Settings for the flow elements', 'NlsHunter'),
    'panel' => $panel
  ]);

  /**
   * Add the Flow element image
   */
  $wp_customize->add_setting('setting_nls_flow_element_field_image_' . $index, array(
    'default' => '',
    'type' => 'option'
  ));

  /**
   * Add the Flow element image
   */
  $wp_customize->add_control(
    new WP_Customize_Image_Control(
      $wp_customize,
      'control_nls_flow_element_field_image_' . $index,
      array(
        'label' => __('Image', 'NlsHunter'),
        'section' => $section->id,
        'settings' => 'setting_nls_flow_element_field_image_' . $index,
      )
    )
  );

  /**
   * Add the Flow element title
   */
  $wp_customize->add_setting('nls_flow_element_field_title_' . $index, array(
    'default' => '',
    'type' => 'option',
    'sanitize_callback' => 'our_sanitize_function',
  ));

  /**
   * Add the Flow element title
   */
  $wp_customize->add_control('nls_flow_element_field_title_' . $index, array(
    'label' => __('Title', 'NlsHunter'),
    'section' => $section->id,
    'settings' => 'nls_flow_element_field_title_' . $index,
    'type' => 'text'
  ));

  /**
   * Add the Flow element content
   */
  $wp_customize->add_setting('nls_flow_element_field_subtitle_' . $index, array(
    'default' => '',
    'type' => 'option',
    'sanitize_callback' => 'our_sanitize_function',
  ));

  /**
   * Add the Flow element content
   */
  $wp_customize->add_control('nls_flow_element_field_subtitle_' . $index, array(
    'label' => __('Subtitle', 'NlsHunter'),
    'section' => $section->id,
    'settings' => 'nls_flow_element_field_subtitle_' . $index,
    'type' => 'text'
  ));
}

function add_flow_elements_general_section($wp_customize, $panel)
{
  /**
   * Add the new Flow section
   */
  $section = $wp_customize->add_section('nls_flow_general', [
    'title' => __('Niloos FBF Flow - General', 'NlsHunter'),
    'description' => __('Settings for the flow elements', 'NlsHunter'),
    'panel' => $panel
  ]);

  /**
   * Add the separator image
   */
  $wp_customize->add_setting('nls_flow_element_separete_image', array(
    'default' => '',
    'type' => 'option',
  ));

  $wp_customize->add_control(
    new WP_Customize_Image_Control(
      $wp_customize,
      'nls_flow_element_separete_image',
      array(
        'label' => __('Separator Image', 'NlsHunter'),
        'section' => $section->id,
        'settings' => 'nls_flow_element_separete_image',
      )
    )
  );

  /**
   * Add the separator image size
   */
  $wp_customize->add_setting('nls_flow_element_media_image_size', array(
    'default' => 42,
    'type' => 'option',
  ));

  $wp_customize->add_control('nls_flow_element_media_image_size', array(
    'label' => __('Separetor image width (px)', 'NlsHunter'),
    'section' => 'nls_flow_general',
    'settings' => 'nls_flow_element_media_image_size',
    'type' => 'number'
  ));
}


// [nls-fbf-flow numberElements=""] shortcode
function nls_fbf_flow($atts)
{
  $a = shortcode_atts(array(
    'num' => NlsConfig::NLS_FLOW_ELEMENTS
  ), $atts);
  $numberElements = $a['num'];

  $separete_image = get_theme_mod('nls_flow_element_separete_image');
  $image_size = get_theme_mod('nls_flow_element_media_image_size');

  $html = '<section class="nls-fbf-flow-wrapper flex flex-col md:flex-row justify-center items-center bg-primary text-white pt-12 pb-8 gap-8 md:gap-3 lg:gap-8">';

  for ($index = 1; $index <= $numberElements; $index++) {
    $image = get_theme_mod('setting_nls_flow_element_field_image_' . $index);
    $title = get_theme_mod('nls_flow_element_field_title_' . $index);
    $subtitle = get_theme_mod('nls_flow_element_field_subtitle_' . $index);

    $html .= elementDesign($image, $title, $subtitle);
    $html .= $index < $numberElements ? '<img width="' . $image_size . '" src="' . $separete_image . '" role="presentation" />' : '';
  }

  $html .= '</section>';

  return $html;
}

function elementDesign($image, $title, $subtitle)
{
  $html =  '<div class="flow-element-card flex flex-col justify-center items-center min-w-flow">';
  $html .= ' <img class="flow-element-image mb-2 w-32 md:w-24" src="' . $image . '" role="presentation" />';
  $html .= ' <h4 class="flow-element-title font-bold">' . $title . '</h4>';
  $html .= ' <p class="flow-element-subtitle text-2xl">' . $subtitle . '</p>';
  $html .= '</div>';

  return $html;
}

add_shortcode('nls_fbf_flow', 'nls_fbf_flow');

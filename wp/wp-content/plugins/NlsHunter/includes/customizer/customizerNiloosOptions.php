<?php

function add_niloos_options_section($wp_customize, $panel)
{
  /**
   * Add the new Niloos section
   */
  $section = $wp_customize->add_section('nls_more_options', [
    'title' => __('Niloos Options', 'NlsHunter'),
    'description' => __('Niloos Options', 'NlsHunter'),
    'panel' => $panel
  ]);

  /**
   * Add the Directory Service Setting
   */
  $wp_customize->add_setting('setting_nls_job_count', array(
    'default' => 20,
    'type' => 'theme_mod',
  ));

  $wp_customize->add_control('control_nls_job_count', array(
    'label' => __('Set Jobs pre page', 'NlsHunter'),
    'section' => $section->id,
    'settings' => 'setting_nls_job_count',
    'type' => 'number'
  ));
}

<?php
include_once NLS__PLUGIN_PATH . '/includes/Hunter/NlsConfig.php';

function add_niloos_options_section($wp_customize, $panel)
{
  /**
   * Add the new Niloos section
   */
  $section = $wp_customize->add_section('nls_more_options', [
    'title' => __('Options', 'NlsHunter'),
    'description' => __('Options', 'NlsHunter'),
    'panel' => $panel
  ]);

  /**
   * Add the Jobs Count
   */
  $wp_customize->add_setting('setting_' . NlsConfig::NLS_JOBS_COUNT, array(
    'default' => 20,
    'type' => 'option',
  ));

  $wp_customize->add_control('control_' . NlsConfig::NLS_JOBS_COUNT, array(
    'label' => __('Jobs per page', 'NlsHunter'),
    'section' => $section->id,
    'settings' => 'setting_' . NlsConfig::NLS_JOBS_COUNT,
    'type' => 'number'
  ));

  /**
   * Add the Hot Jobs Count
   */
  $wp_customize->add_setting('setting_' . NlsConfig::NLS_HOT_JOBS_COUNT, array(
    'default' => 6,
    'type' => 'option',
  ));

  $wp_customize->add_control('control_' . NlsConfig::NLS_HOT_JOBS_COUNT, array(
    'label' => __('Hot Jobs Count', 'NlsHunter'),
    'section' => $section->id,
    'settings' => 'setting_' . NlsConfig::NLS_HOT_JOBS_COUNT,
    'type' => 'number'
  ));
}

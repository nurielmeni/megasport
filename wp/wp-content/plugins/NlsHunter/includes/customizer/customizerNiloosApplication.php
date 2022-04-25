<?php

function add_niloos_application_section($wp_customize, $panel)
{
  /**
   * Add the new Niloos section
   */
  $section = $wp_customize->add_section('nls_application', [
    'title' => __('Application', 'NlsHunter'),
    'description' => __('Application', 'NlsHunter'),
    'panel' => $panel
  ]);

  /**
   * Add the Consumer
   */
  $wp_customize->add_setting('setting_nls_consumer', array(
    'default' => '',
    'type' => 'option',
  ));

  $wp_customize->add_control('control_nls_consumer', array(
    'label' => __('Set the application consumer', 'NlsHunter'),
    'section' => $section->id,
    'settings' => 'setting_nls_consumer',
    'type' => 'text'
  ));

  /**
   * Add the Supplier Id
   */
  $wp_customize->add_setting('setting_nls_supplier_id', array(
    'default' => '',
    'type' => 'option',
  ));

  $wp_customize->add_control('control_nls_supplier_id', array(
    'label' => __('Set the application supplier ID', 'NlsHunter'),
    'section' => $section->id,
    'settings' => 'setting_nls_supplier_id',
    'type' => 'text'
  ));

  /**
   * Add the Hot Jobs Supplier Id
   */
  // $wp_customize->add_setting('setting_nls_hot_supplier_id', array(
  //   'default' => '',
  //   'type' => 'option',
  // ));

  // $wp_customize->add_control('control_nls_hot_supplier_id', array(
  //   'label' => __('Set the application Hot Jobs supplier ID', 'NlsHunter'),
  //   'section' => $section->id,
  //   'settings' => 'setting_nls_hot_supplier_id',
  //   'type' => 'text'
  // ));

}

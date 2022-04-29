<?php
include_once NLS__PLUGIN_PATH . '/includes/Hunter/NlsConfig.php';

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
  $wp_customize->add_setting('setting_' . NlsConfig::NLS_CONSUMER, array(
    'default' => '',
    'type' => 'option',
  ));

  $wp_customize->add_control('control_' . NlsConfig::NLS_CONSUMER, array(
    'label' => __('Set the application consumer', 'NlsHunter'),
    'section' => $section->id,
    'settings' => 'setting_' . NlsConfig::NLS_CONSUMER,
    'type' => 'text'
  ));

  /**
   * Add the Supplier Id
   */
  $wp_customize->add_setting('setting_' . NlsConfig::NLS_SUPPLIER_ID, array(
    'default' => '',
    'type' => 'option',
  ));

  $wp_customize->add_control('control_' . NlsConfig::NLS_SUPPLIER_ID, array(
    'label' => __('Set the application supplier ID', 'NlsHunter'),
    'section' => $section->id,
    'settings' => 'setting_' . NlsConfig::NLS_SUPPLIER_ID,
    'type' => 'text'
  ));

  /**
   * Add the Hot Jobs Supplier Id
   */
  // $wp_customize->add_setting('setting_' . NlsConfig::NLS_HOT_JOBS_SUPPLIER_ID, array(
  //   'default' => '',
  //   'type' => 'option',
  // ));

  // $wp_customize->add_control('control_' . NlsConfig::NLS_HOT_JOBS_SUPPLIER_ID, array(
  //   'label' => __('Set the application Hot Jobs supplier ID', 'NlsHunter'),
  //   'section' => $section->id,
  //   'settings' => 'setting_' . NlsConfig::NLS_HOT_JOBS_SUPPLIER_ID,
  //   'type' => 'text'
  // ));

  /**
   * Add the To Web Mail
   */
  $wp_customize->add_setting('setting_' . NlsConfig::NLS_TO_WEBMAIL, array(
    'default' => '',
    'type' => 'option',
  ));

  $wp_customize->add_control('control_' . NlsConfig::NLS_TO_WEBMAIL, array(
    'label' => __('Web Mail', 'NlsHunter'),
    'section' => $section->id,
    'settings' => 'setting_' . NlsConfig::NLS_TO_WEBMAIL,
    'type' => 'text'
  ));

  /**
   * Add the To Bcc Mail
   */
  $wp_customize->add_setting('setting_' . NlsConfig::NLS_BCC_MAIL, array(
    'default' => '',
    'type' => 'option',
  ));

  $wp_customize->add_control('control_' . NlsConfig::NLS_BCC_MAIL, array(
    'label' => __('Bcc Mail', 'NlsHunter'),
    'section' => $section->id,
    'settings' => 'setting_' . NlsConfig::NLS_BCC_MAIL,
    'type' => 'text'
  ));
}

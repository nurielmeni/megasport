<?php
include_once NLS__PLUGIN_PATH . '/includes/Hunter/NlsConfig.php';

function add_niloos_auth_section($wp_customize, $panel)
{
  /**
   * Add the new Niloos section
   */
  $section = $wp_customize->add_section('nls_auth', [
    'title' => __('Auth', 'NlsHunter'),
    'description' => __('Auth', 'NlsHunter'),
    'panel' => $panel
  ]);

  /**
   * Add the Domain
   */
  $wp_customize->add_setting('setting_' . NlsConfig::NLS_DOMAIN, array(
    'default' => '',
    'type' => 'option',
  ));

  $wp_customize->add_control('control_' . NlsConfig::NLS_DOMAIN, array(
    'label' => __('Domain', 'NlsHunter'),
    'section' => $section->id,
    'settings' => 'setting_' . NlsConfig::NLS_DOMAIN,
    'type' => 'text'
  ));

  /**
   * Add the User
   */
  $wp_customize->add_setting('setting_' . NlsConfig::NLS_USER, array(
    'default' => '',
    'type' => 'option',
  ));

  $wp_customize->add_control('control_' . NlsConfig::NLS_USER, array(
    'label' => __('User', 'NlsHunter'),
    'section' => $section->id,
    'settings' => 'setting_' . NlsConfig::NLS_USER,
    'type' => 'text'
  ));

  /**
   * Add the User
   */
  $wp_customize->add_setting('setting_' . NlsConfig::NLS_PASSWORD, array(
    'default' => '',
    'type' => 'option',
  ));

  $wp_customize->add_control('control_' . NlsConfig::NLS_PASSWORD, array(
    'label' => __('Password', 'NlsHunter'),
    'section' => $section->id,
    'settings' => 'setting_' . NlsConfig::NLS_PASSWORD,
    'type' => 'text'
  ));
}

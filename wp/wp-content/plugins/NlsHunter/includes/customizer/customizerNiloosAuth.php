<?php

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
  $wp_customize->add_setting('setting_nls_domain', array(
    'default' => '',
    'type' => 'option',
  ));

  $wp_customize->add_control('control_nls_domain', array(
    'label' => __('Domain', 'NlsHunter'),
    'section' => $section->id,
    'settings' => 'setting_nls_domain',
    'type' => 'text'
  ));

  /**
   * Add the User
   */
  $wp_customize->add_setting('setting_nls_user', array(
    'default' => '',
    'type' => 'option',
  ));

  $wp_customize->add_control('control_nls_user', array(
    'label' => __('User', 'NlsHunter'),
    'section' => $section->id,
    'settings' => 'setting_nls_user',
    'type' => 'text'
  ));

  /**
   * Add the User
   */
  $wp_customize->add_setting('setting_nls_password', array(
    'default' => '',
    'type' => 'option',
  ));

  $wp_customize->add_control('control_nls_password', array(
    'label' => __('Password', 'NlsHunter'),
    'section' => $section->id,
    'settings' => 'setting_nls_password',
    'type' => 'text'
  ));
}

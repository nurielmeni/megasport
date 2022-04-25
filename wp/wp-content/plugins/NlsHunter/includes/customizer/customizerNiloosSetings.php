<?php

function add_niloos_settings_section($wp_customize, $panel)
{
  /**
   * Add the new Niloos section
   */
  $section = $wp_customize->add_section('nls_settings_service', [
    'title' => __('Services - WSDL', 'NlsHunter'),
    'description' => __('Niloos WSDL services', 'NlsHunter'),
    'panel' => $panel
  ]);

  /**
   * Add the Directory Service Setting
   */
  $wp_customize->add_setting('setting_nls_directory_service', array(
    'default' => 'https://hunterdirectory.hunterhrms.com/DirectoryManagementService.svc?wsdl',
    'type' => 'option',
  ));

  $wp_customize->add_control('control_nls_directory_service', array(
    'label' => __('Set WSDL for directory service', 'NlsHunter'),
    'section' => $section->id,
    'settings' => 'setting_nls_directory_service',
    'type' => 'url'
  ));

  /**
   * Add Cards Service Setting
   */
  $wp_customize->add_setting('setting_nls_cards_service', array(
    'default' => 'https://huntercards.hunterhrms.com/HunterCards.svc?wsdl',
    'type' => 'option',
  ));

  $wp_customize->add_control('control_nls_cards_service', array(
    'label' => __('Set WSDL for cards service', 'NlsHunter'),
    'section' => $section->id,
    'settings' => 'setting_nls_cards_service',
    'type' => 'url'
  ));

  /**
   * Add Directory Security Setting
   */
  $wp_customize->add_setting('setting_nls_security_service', array(
    'default' => 'https://hunterdirectory.hunterhrms.com/SecurityService.svc?wsdl',
    'type' => 'option',
  ));

  $wp_customize->add_control('control_nls_security_service', array(
    'label' => __('Set WSDL for security service', 'NlsHunter'),
    'section' => $section->id,
    'settings' => 'setting_nls_security_service',
    'type' => 'url'
  ));

  /**
   * Add Directory Serach Setting
   */
  $wp_customize->add_setting('setting_nls_search_service', array(
    'default' => 'https://huntersearchengine.hunterhrms.com/SearchEngineHunterService.svc?wsdl',
    'type' => 'option',
  ));

  $wp_customize->add_control('control_nls_search_service', array(
    'label' => __('Set WSDL for search service', 'NlsHunter'),
    'section' => $section->id,
    'settings' => 'setting_nls_search_service',
    'type' => 'url'
  ));
}

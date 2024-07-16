<?php

/**
 * Handles the Download
 *
 * @link       ekn.dev
 * @since      1.0.0
 *
 * @package    Wp_Dispatcher_Shortcode
 * @subpackage Wp_Dispatcher_Shortcode/admin
 */

/**
 * Class WordPress_Plugin_Template_Settings
 *
 */
class Wp_Dispatcher_Downloader
{

  /**
   * The ID of this plugin.
   *
   * @since    1.0.0
   * @access   private
   * @var      string    $plugin_name    The ID of this plugin.
   */
  private $plugin_name;

  /**
   * The version of this plugin.
   *
   * @since    1.0.0
   * @access   private
   * @var      string    $version    The current version of this plugin.
   */
  private $version;

  /**
   * Initialize the class and set its properties.
   *
   * @since    1.0.0
   * @param      string    $plugin_name       The name of this plugin.
   * @param      string    $version    The version of this plugin.
   */
  public function __construct($plugin_name, $version)
  {

    $this->plugin_name = $plugin_name;
    $this->version = $version;
  }

  /**
   * 
   *
   * @since    1.0.0
   */
  public function dispatcher_add_rewrites()
  {


    //add_rewrite_rule(
    //'^download-has-expired/?',
    //'index.php?expired=true',
    //'top');    
  }

  function dispatcher_rewrite_add_var($vars)
  {
    $vars[] = 'expired';
    $vars[] = 'resolve';

    return $vars;
  }

  function dispatcher_rewrite_catch_resolve()
  {
    if (get_query_var('resolve')) {
      $sanitized_var = sanitize_text_field(get_query_var('resolve'));
      $this->resolve_hash_id($sanitized_var);
      exit();
    }
    
    if (get_query_var('expired')) {
      _e('Download link has expired', 'wp-dispatcher');
      exit();
    }
  }

  public function maybe_flush_rewrite_rules()
  {

    if (get_option('prefix_do_flush_rewrite')) {
      flush_rewrite_rules();
      delete_option('prefix_do_flush_rewrite');
    }
  }

  public function resolve_hash_id($hash)
  {

    global $wpdb;
    $link_table_name = $wpdb->prefix . 'dispatcher_links';
    $uploads_table_name = $wpdb->prefix . 'dispatcher_uploads';

    $link = $wpdb->get_row("SELECT * FROM {$link_table_name} WHERE hash_id = '{$hash}'");

    if ((null !== $link)) {

      if (new DateTime($link->expires) < new DateTime()) {
        wp_safe_redirect(get_site_url() . '/download-is-not-available');
        exit();
      }

      $upload = $wpdb->get_row("SELECT * FROM {$uploads_table_name} WHERE id = {$link->upload_id}");
      $count = $upload->count + 1;
      $wpdb->update(
        $uploads_table_name,
        array('count' => $count),
        array('id' => $link->upload_id),
        array('%d'),
        array('%d')
      );
      $this->stream_download($upload->filename);
    }
    
    else {
      global $wp_query;
      $wp_query->set_404();
      status_header(404);
      get_template_part(404);
      exit();
    }
  }


  public function stream_download($filename)
  {

    $upload_dir = wp_upload_dir();

    $file = $upload_dir['basedir'] . '/' . "wp-dispatcher/" . $filename;

    if (file_exists($file)) {
      header('Content-Description: File Transfer');
      header('Content-Type: application/octet-stream');
      header('Content-Disposition: attachment; filename="' . basename($file) . '"');
      header('Expires: 0');
      header('Cache-Control: must-revalidate');
      header('Pragma: public');
      header('Content-Length: ' . filesize($file));
      readfile($file);
      exit;
    }

    global $wp_query;
    $wp_query->set_404();
    status_header(404, "File could not be found.");
    get_template_part(404);
    exit();
  }
}

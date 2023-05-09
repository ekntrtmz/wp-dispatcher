<?php

if (!class_exists('Custom_WP_List_Table')) {
  // require_once( ABSPATH . 'wp-content/plugins/wp-dispatcher/libraries/class-wp-list-table.php' );
  require_once plugin_dir_path(dirname(__FILE__)) . 'libraries/class-wp-list-table.php';
}

/**
 * Class for displaying custom Uploads
 * in a WordPress-like Admin Table with row actions to 
 * perform user meta opeations
 */
class Uploads_List_Table extends Custom_WP_List_Table
{

  public function search_box($text, $input_id)
  {
  } // Remove search box
  //protected function pagination( $which ){}        // Remove pagination
  //protected function display_tablenav( $which ){}  // Remove navigation


  function __construct()
  {
    global $status, $page;

    //Set parent defaults
    parent::__construct(array(
      'singular'  => 'upload',     //singular name of the listed records
      'plural'    => 'uploads',    //plural name of the listed records
      'ajax'      => false        //does this table support ajax?
    ));
  }

  function column_default($item, $column_name)
  {
    switch ($column_name) {
      case 'date':
      case 'count':
      case 'author':
      case 'filename':
        return $item[$column_name];
      case 'shortcode':
        return '<input type="text"  value="[wp_dispatch id=' . $item["id"] . ']" readonly>';
      default:
        return print_r($item, true); //Show the whole array for troubleshooting purposes
    }
  }


  /** ************************************************************************
   * REQUIRED! This method dictates the table's columns and titles. This should
   * return an array where the key is the column slug (and class) and the value 
   * is the column's title text. If you need a checkbox for bulk actions, refer
   * to the $columns array below.
   * 
   * The 'cb' column is treated differently than the rest. If including a checkbox
   * column in your table you must create a column_cb() method. If you don't need
   * bulk actions or checkboxes, simply leave the 'cb' entry out of your array.
   * 
   * @see WP_List_Table::::single_row_columns()
   * @return array An associative array containing column information: 'slugs'=>'Visible Titles'
   **************************************************************************/
  function get_columns()
  {
    $columns = array(
      'filename'     => __('Filename', 'wp-dispatcher'),
      'author'    => __('Author', 'wp-dispatcher'),
      'date'  => __('Uploaded', 'wp-dispatcher'),
      'shortcode' => __('Shortcode', 'wp-dispatcher'),
      'count' =>  __('Downloads', 'wp-dispatcher')
    );
    return $columns;
  }

  public function get_sortable_columns()
  {
    return [
      'date' => array('date', false),
      'count' => array('count', false)
    ];
  }

  private function sort_data($a, $b)
  {
    $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'id'; //If no sort, default to id
    $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
    $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
    return ($order === 'asc') ? $result : -$result; //Send final sort direction to usort
  }

  function column_cb($item)
  {
    return '<span class="dashicons dashicons-download"></span>';
  }

  /** ************************************************************************
   * REQUIRED! This is where you prepare your data for display. This method will
   * usually be used to query the database, sort and filter the data, and generally
   * get it ready to be displayed. At a minimum, we should set $this->items and
   * $this->set_pagination_args(), although the following properties and methods
   * are frequently interacted with here...
   * 
   * @global WPDB $wpdb
   * @uses $this->_column_headers
   * @uses $this->items
   * @uses $this->get_columns()
   * @uses $this->get_sortable_columns()
   * @uses $this->get_pagenum()
   * @uses $this->set_pagination_args()
   **************************************************************************/
  function prepare_items()
  {

    global $wpdb; //This is used only if making any database queries

    /**
     * First, lets decide how many records per page to show
     */
    $per_page = 5;


    /**
     * REQUIRED. Now we need to define our column headers. This includes a complete
     * array of columns to be displayed (slugs & titles), a list of columns
     * to keep hidden, and a list of columns that are sortable. Each of these
     * can be defined in another method (as we've done here) before being
     * used to build the value for our _column_headers property.
     */
    $columns = $this->get_columns();
    $hidden = array();
    $sortable = $this->get_sortable_columns();


    /**
     * REQUIRED. Finally, we build an array to be used by the class for column 
     * headers. The $this->_column_headers property takes an array which contains
     * 3 other arrays. One for all columns, one for hidden columns, and one
     * for sortable columns.
     */
    $this->_column_headers = array($columns, $hidden, $sortable);


    /**
     * Optional. You can handle your bulk actions however you see fit. In this
     * case, we'll handle them within our package just to keep things clean.
     */
    //$this->process_bulk_action();


    /**
     * Instead of querying a database, we're going to fetch the example data
     * property we created for use in this plugin. This makes this example 
     * package slightly different than one you might build on your own. In 
     * this example, we'll be using array manipulation to sort and paginate 
     * our data. In a real-world implementation, you will probably want to 
     * use sort and pagination data to build a custom query instead, as you'll
     * be able to use your precisely-queried data immediately.
     */
    //$data = $this->example_data;


    /**
     * This checks for sorting input and sorts the data in our array accordingly.
     * 
     * In a real-world situation involving a database, you would probably want 
     * to handle sorting by passing the 'orderby' and 'order' values directly 
     * to a custom query. The returned data will be pre-sorted, and this array
     * sorting technique would be unnecessary.
     */
    // function usort_reorder($a,$b){
    //     $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'title'; //If no sort, default to title
    //     $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
    //     $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
    //     return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
    // }
    // usort($data, 'usort_reorder');


    /***********************************************************************
     * ---------------------------------------------------------------------
     * vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv
     * 
     * In a real-world situation, this is where you would place your query.
     *
     * For information on making queries in WordPress, see this Codex entry:
     * http://codex.wordpress.org/Class_Reference/wpdb
     * 
     * ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
     * ---------------------------------------------------------------------
     **********************************************************************/

    $table_name = $wpdb->prefix . 'dispatcher_uploads';

    $data = $wpdb->get_results("SELECT * FROM {$table_name} ORDER BY id DESC", ARRAY_A);
    usort($data, array(&$this, 'sort_data'));



    /**
     * REQUIRED for pagination. Let's figure out what page the user is currently 
     * looking at. We'll need this later, so you should always include it in 
     * your own package classes.
     */
    $current_page = $this->get_pagenum();

    /**
     * REQUIRED for pagination. Let's check how many items are in our data array. 
     * In real-world use, this would be the total number of items in your database, 
     * without filtering. We'll need this later, so you should always include it 
     * in your own package classes.
     */
    $total_items = count($data);


    /**
     * The WP_List_Table class does not handle pagination for us, so we need
     * to ensure that the data is trimmed to only the current page. We can use
     * array_slice() to 
     */
    $data = array_slice($data, (($current_page - 1) * $per_page), $per_page);



    /**
     * REQUIRED. Now we can add our *sorted* data to the items property, where 
     * it can be used by the rest of the class.
     */
    $this->items = $data;


    /**
     * REQUIRED. We also have to register our pagination options & calculations.
     */
    $this->set_pagination_args(array(
      'total_items' => $total_items,                  //WE have to calculate the total number of items
      'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
      'total_pages' => ceil($total_items / $per_page)   //WE have to calculate the total number of pages
    ));
  }

  /** Text displayed when no customer data is available */
  public function no_items()
  {
    _e('No uploads available.', 'wp_dispatcher');
  }
}

<?php defined('SYSPATH') or die('No direct script access.');

class Model extends Model_Core {

  /**
   * The database instance.
   */
  protected $db;

  /**
   * Class constructor.
   *
   * @access	public
   * @return	void
   */
  public function __construct() {
    $this->db = Database::instance();
  }

  public function result_as_array($result_set) {
    $array_results = array();
    if($result_set) {
      foreach ($result_set as $result) {
        $array_results[]=is_array($result)?$result:$result->to_array();
      }
    }
    return count($array_results)==1?$array_results[0]:$array_results;
  }
  /**
   *
   * @param array $key_prefixes ex: array('site','building')
   * @param array $mixed_array_result ex:
   *    ex:
   *  Array
   *(
   *    [space_name] => 3rd floor
   *    [space_index] => 3
   *    [space_img_uri] => mtview0_3.gif
   *    [space_active] => 1
   *    [building_name] => Main Office
   *    [building_lat] => 37.3893
   *    [building_long] => -122.083
   *    [building_active] => 1
   *)
   * @return array
   *    ex:
   * Array
   * (
   *     [space] => Array
   *         (
   *             [name] => 3rd floor
   *             [index] => 3
   *             [img_uri] => mtview0_3.gif
   *             [active] => 1
   *         )
   *
   *     [building] => Array
   *         (
   *             [name] => Main Office
   *             [lat] => 37.3893
   *             [long] => -122.083
   *             [active] => 1
   *         )
   *
   * )
   */
  public function split_out_array_result(array $key_prefixes, array $mixed_array_result) {
      $return = array();
      foreach ($mixed_array_result as $key => $value) { // $key = 'site_name'
          $found = false;
          foreach ($key_prefixes as $prefix) { // $prefix = 'site'
              if(substr($key, 0, strlen($prefix))== $prefix) {
                  $return[$prefix][substr($key, strlen($prefix)+1)]=$value;
                  $found = true;
              }
          }
          if( ! $found) {
              $return[$key] = $value;
          }
      }
      return $return;
  }
  protected function get_trimmed_allowed(array $data, array $allowed_fields) {
    $allowed_fields = array_fill_keys($allowed_fields,null);
    $update_data = array_intersect_key($data,$allowed_fields);
    return array_map('trim',$update_data);
  }

}
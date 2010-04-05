<?php defined('SYSPATH') or die('No direct script access.');

class Site_Model {

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
  protected function get_trimmed_allowed(array $data, array $allowed_fields) {
    $allowed_fields = array_fill_keys($allowed_fields,null);
    $update_data = array_intersect_key($data,$allowed_fields);
    return array_map('trim',$update_data);
  }

}
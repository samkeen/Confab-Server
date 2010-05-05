<?php defined('SYSPATH') or die('No direct script access.');

class Site_Model extends Model {

  /**
   * Class constructor.
   *
   * @access	public
   * @return	void
   */
  public function __construct() {
    parent::__construct();
  }

  public function select_list() {
    $buildings = $this->db->select('*')
      ->from('buildings')
      ->where(
        array(
          'buildings.active' => '1'
        )
      )
      ->get();
    return $this->result_as_array($buildings);
  }

  

}
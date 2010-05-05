<?php defined('SYSPATH') or die('No direct script access.');

class Space_Model extends Model {

  /**
   * Class constructor.
   *
   * @access	public
   * @return	void
   */
  public function __construct() {
    parent::__construct();
  }

  public function get_people($space_id) {
    // get the info for this floor
    $space = $this->db->select('spaces.*')
      ->from('spaces')
      ->where(
        array(
          'spaces.id' => $space_id,
          'spaces.active' => '1'
        )
      )
      ->get();
    $space = $this->result_as_array($space);
    // add all the people
    $people = 
      $this->db->select('people.*')
      ->from('people')
      ->where(
        array(
          'space_id' => $space['id'],
          'people.active' => '1'
        )
      )
      ->get();
    $space['people'] = $this->result_as_array($people);
    return $space;
  }

}
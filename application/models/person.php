<?php defined('SYSPATH') or die('No direct script access.');

class Person_Model extends Model {

  protected $belongs_to = array('space');

  public function __construct() {
    // load database library into $this->db (can be omitted if not required)
    parent::__construct();
  }

  public function get_space_by_email($email) {
    $focus_person = null;
    $people =
      $this->db->select('people.*')
      ->from('people')
      ->where(array('active' => 1))
      ->get();
    $people = $this->result_as_array($people);
    foreach ($people as $person) {
      if($person['email']==$email) {
        $focus_person=$person;
        break;
      }
    }
    $location_info =
      $this->db->select(
      'spaces.name AS space_name, spaces.index AS space_index,
       spaces.img_uri AS space_img_uri, spaces.active AS space_active,
       buildings.name AS building_name, buildings.lat AS building_lat,
       buildings.long AS building_long, buildings.active AS building_active,
       sites.name AS site_name, sites.lat AS site_lat,
       sites.long AS site_long, sites.active AS site_active'
      )
      ->from('spaces')
      ->join('buildings','spaces.building_id', 'buildings.id')
      ->join('sites','buildings.site_id', 'sites.id')
      ->where('spaces.id', $person['space_id'])
      ->get();
    
    $location_info = $this->result_as_array($location_info);
    $location_info = $this->split_out_array_result(array('space','building','site'), $location_info);
    return array(
        'people'=>$people,'space'=>$location_info['space'],
        'building'=>$location_info['building'], 'site'=>$location_info['site'],
        'focus'=>$focus_person
    );
  }

  public function save(array $person=array(), $person_id = null) {
    $result = array(
      'success'=>false,
      'error' => ''
    );
    
    $valid = true;
    $person = $this->get_trimmed_allowed(
      $person,
      array('space_id','email','x','y','active')
    );
    if(isset($person['space_id']) && !valid::digit($person['space_id'])) {
      $valid = false;
    }
    if(isset($person['email']) && !valid::email($person['email'])) {
      $valid = false;
    }
    if(isset($person['x']) && !valid::numeric($person['x'])) {
      $valid = false;
    }
    if(isset($person['y']) && !valid::numeric($person['y'])) {
      $valid = false;
    }
    if(isset($person['active']) && !valid::digit($person['active'])) {
      $valid = false;
    }
    if($valid) {
      if($person_id) {//UPDATE
        $this->db
          ->from('people')
          ->set($person)
          ->where(array('id' => (int)$person_id)
          )->update();
        $result['success']=true;
        
      } else { // INSERT
        $new_person = $this->db
          ->from('people')
          ->set($person)
          ->insert();
        $result['success']=true;
        $person_id = $new_person->insert_id();
      }
      $person = $this->db->select('people.*')
        ->from('people')
        ->where(array('id' => $person_id))
        ->get();
      $person = $this->result_as_array($person);
      $result['person']=$person;
    } else {
      $result['error']="The supplied data was invalid";
    }

    
    return $result;
    
  }
  public function remove($person_id) {
    $result = array(
      'success'=>false,
      'error' => ''
    );
    $this->db
      ->from('people')
      ->where(
        array(
          'id' => $person_id
        )
      )
      ->delete();
    $result['success']=true;
    return $result;
  }


}
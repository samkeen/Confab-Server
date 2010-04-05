<?php defined('SYSPATH') or die('No direct script access.');

class Person_Model extends Site_Model {

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
    $space =
      $this->db->select('spaces.*')
      ->from('spaces')
      ->where('id', $person['space_id'])
      ->get();
    $space = $this->result_as_array($space);
    return array('people'=>$people,'space'=>$space,'focus'=>$focus_person);
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
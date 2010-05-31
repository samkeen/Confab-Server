<?php defined('SYSPATH') or die('No direct script access.');

class Person_Model extends Model {

  protected $belongs_to = array('space');

  public function __construct() {
    // load database library into $this->db (can be omitted if not required)
    parent::__construct();
  }

  public function get_space_by_email($email) {
    $focus_marker = null;
    $markers =
      $this->db->select('markers.*')
      ->from('markers')
      ->where(array('active' => 1))
      ->get();
    $markers = $this->result_as_array($markers);
    foreach ($markers as $marker) {
      if($marker['email']==$email) {
        $focus_marker=$marker;
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
      ->where('spaces.id', $marker['space_id'])
      ->get();
    
    $location_info = $this->result_as_array($location_info);
    $location_info = $this->split_out_array_result(array('space','building','site'), $location_info);
    return array(
        'markers'=>$markers,'space'=>$location_info['space'],
        'building'=>$location_info['building'], 'site'=>$location_info['site'],
        'focus'=>$focus_marker
    );
  }

  public function save(array $marker=array(), $marker_id = null) {
    $result = array(
      'success'=>false,
      'error' => ''
    );
    
    $valid = true;
    $marker = $this->get_trimmed_allowed(
      $marker,
      array('space_id','email','x','y','active')
    );
    if(isset($marker['space_id']) && !valid::digit($marker['space_id'])) {
      $valid = false;
    }
    if(isset($marker['email']) && !valid::email($marker['email'])) {
      $valid = false;
    }
    if(isset($marker['x']) && !valid::numeric($marker['x'])) {
      $valid = false;
    }
    if(isset($marker['y']) && !valid::numeric($marker['y'])) {
      $valid = false;
    }
    if(isset($marker['active']) && !valid::digit($marker['active'])) {
      $valid = false;
    }
    if($valid) {
      if($marker_id) {//UPDATE
        $this->db
          ->from('markers')
          ->set($marker)
          ->where(array('id' => (int)$marker_id)
          )->update();
        $result['success']=true;
        
      } else { // INSERT
        $new_marker = $this->db
          ->from('markers')
          ->set($marker)
          ->insert();
        $result['success']=true;
        $marker_id = $new_marker->insert_id();
      }
      $marker = $this->db->select('markers.*')
        ->from('markers')
        ->where(array('id' => $marker_id))
        ->get();
      $marker = $this->result_as_array($marker);
      $result['marker']=$marker;
    } else {
      $result['error']="The supplied data was invalid";
    }

    
    return $result;
    
  }
  public function remove($marker_id) {
    $result = array(
      'success'=>false,
      'error' => ''
    );
    $this->db
      ->from('markers')
      ->where(
        array(
          'id' => $marker_id
        )
      )
      ->delete();
    $result['success']=true;
    return $result;
  }


}
<?php defined('SYSPATH') or die('No direct script access.');

class Markers_Controller extends Template_Controller {
  

  public function index($id=null) {

    $this->template->title = 'Seating::Markers';
    $this->template->content = new View('pages/markers');

  }
  public function space_for($email=null) {
    $email = preg_replace('/\.json$/i', '', $email);
    $placements_for_space = null;
    if($email!==null) {
      $placement = new Placement_Model;
      $placements_for_space = $placement->get_by_email($email);
    } else {

    }

    if ($this->is_json_request()) {
      $this->json_response($placements_for_space);
    } else {
      $this->template->title = 'Seating::Markers';
      $this->template->content = new View('pages/markers');
    }
  }
  public function save($marker_id=null) {
    $marker_id = (int)preg_replace('/\.json$/i', '', $marker_id);
    $result = null;
    
    $markers = new Marker_Model;
    $result = $markers->save($this->input->get('marker'), $marker_id);
    
    if($result['success']) {

    } else {

    }
    if ($this->is_json_request()) {
      $this->json_response($result);
    }
//    die($update_context);
  }
  public function remove($marker_id=null) {
    $marker_id = (int)preg_replace('/\.json$/i', '', $marker_id);
    $result = null;
    $markers = new Marker_Model;
    $result = $markers->remove($marker_id);

    if ($this->is_json_request()) {
      $this->json_response($result);
    }
//    die($update_context);
  }

}
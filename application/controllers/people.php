<?php defined('SYSPATH') or die('No direct script access.');

class People_Controller extends Template_Controller {
  

  public function index($id=null) {

    $this->template->title = 'Seating::People';
    $this->template->content = new View('pages/people');

  }
  public function space_for($email=null) {
    $email = preg_replace('/\.json$/i', '', $email);
    $peep = null;
    if($email!==null) {
      $people = new Person_Model;
      $peep = $people->get_space_by_email($email);
      
    } else {

    }
    
    if ($this->is_json_request()) {
      $this->json_response($peep);
    } else {
      $this->template->title = 'Seating::People';
      $this->template->content = new View('pages/people');


    }
  }
  public function save($person_id=null) {
    $person_id = (int)preg_replace('/\.json$/i', '', $person_id);
    $result = null;
    
    $people = new Person_Model;
    $result = $people->save($this->input->get('person'), $person_id);
    
    if($result['success']) {

    } else {

    }
    if ($this->is_json_request()) {
      $this->json_response($result);
    }
//    die($update_context);
  }
  public function remove($person_id=null) {
    $person_id = (int)preg_replace('/\.json$/i', '', $person_id);
    $result = null;
    $people = new Person_Model;
    $result = $people->remove($person_id);

    if ($this->is_json_request()) {
      $this->json_response($result);
    }
//    die($update_context);
  }

}
<?php defined('SYSPATH') or die('No direct script access.');

class Spaces_Controller extends Site_Controller {

  public function index($id=null) {

    $this->template->title = 'Seating::Spaces';
    $this->template->content = new View('pages/spaces');

  }
  public function markers($space_id=null) {
    $space_id = preg_replace('/\.json$/i', '', $space_id);
    $space = null;
    if($space_id!==null) {
      $spaces = new Space_Model();
      $space = $spaces->get_people($space_id);
    } else {

    }
    if ($this->is_json_request()) {
      $this->json_response($space);
    } else {
      $this->template->title = 'Seating::Spaces';
      $this->template->content = new View('pages/spaces');
    }
  }
  public function person($email=null) {
    $this->template = new View("space_for");
    $this->template->title = 'Seating::Spaces';
    $this->template->content = new View('pages/spaces');
  }

}
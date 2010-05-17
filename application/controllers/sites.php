<?php defined('SYSPATH') or die('No direct script access.');

class Sites_Controller extends Template_Controller {

  public function index($id=null) {

    $this->template->title = 'Seating::Spaces';
    $this->template->content = new View('pages/spaces');

  }
  
  public function add() {
    
    $this->template->title = 'Sites :: Add';
    $this->template->content = new View('pages/sites_add');
    
    $site = ORM::factory('site');
    
    if ($post = $this->input->post()) {
      if ($site->validate($post)) {
        $site->save();
        url::redirect('sites');
      } else {
        client::validation_results($post->errors());
        client::messageSend("There were errors in some fields", E_USER_WARNING);
      }
    }
    $this->template->content->site = $site;
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
    isset($this->profiler)?$this->profiler->disable():null;
    $this->template = new View("space_for");
    $this->template->title = 'Seating::Spaces';
    $this->template->content = new View('pages/spaces');
  }

}
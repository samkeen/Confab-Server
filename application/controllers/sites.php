<?php defined('SYSPATH') or die('No direct script access.');

class Sites_Controller extends Template_Controller {

    public function index($id=null) {
    
        $this->template->title = 'Seating::Spaces';
        $this->template->content = new View('pages/sites_index');
        
        $sites = ORM::factory('site')->find_all();
        $this->template->content->sites = $sites;
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
    public function edit($edit_id=null) {
        if( ! $edit_id) {
            client::messageSend("Invalid Request", E_USER_WARNING);
            url::redirect('sites');
        }
        
        $this->template->title = 'Sites :: Edit';
        $this->template->content = new View('pages/sites_edit');
        
        $site = ORM::factory('site')->find($edit_id);
        
        if ($post = $this->input->post()) {
            $site->name = $post['name'];
            $site->name = $post['lat'];
            $site->name = $post['long'];
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
  
    public function delete($delete_id) {
        if( ! $delete_id) {
            client::messageSend("Invalid Request", E_USER_WARNING);
            url::redirect('sites');
        }
        $site = ORM::factory('site')->find($delete_id);
        if($site->id) {
            $site->delete($delete_id);
            client::messageSend('The Site "'.html::specialchars($site->name).'" was removed', E_USER_NOTICE);
        } else {
            client::messageSend("Site not found", E_USER_WARNING);
        }
        url::redirect('sites');
  }

}
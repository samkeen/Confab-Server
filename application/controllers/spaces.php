<?php defined('SYSPATH') or die('No direct script access.');

class Spaces_Controller extends Template_Controller {

    public function index($id=null) {

        $this->template->title = 'Seating::Spaces';
        $this->template->content = new View('pages/spaces');

    }
    public function add() {
        $form = array(
                'building_id' => '',
                'name' => '',
                'index' => '',
                'img_uri' => '',
                'active' => ''
        );
        $errors = $form;

        if($_POST) {
            $post = new Validation($_POST);
            $post->pre_filter('trim', true);
            $post->add_rules('buildings_id', 'required', 'digit');
            $post->add_rules('name', 'required');
            $post->add_rules('index', 'required');
            $post->add_rules('img_uri', 'required');
            $post->add_rules('active', 'required');
            
            if ($post->validate()) {
                // check for invilid
                $form = arr::overwrite($form, $post->as_array());

                $markers = new Person_Model;
                $result = $markers->save($this->input->get('marker'), $marker_id);

            } else {
                $form = arr::overwrite($form, $post->as_array());
                client::validation_results(arr::overwrite($errors, $post->errors('hiring_employee_form_validations')));
                client::messageSend("There were errors in some fields", E_USER_WARNING);
            }
        }
        $building = new Building_Model;
        $buildings_list = $building->select_list();
        
        $this->template->title = 'Seating::Spaces::Add';
        $this->template->content = new View('pages/spaces_add');
        $this->template->content->form = $form;
        $this->template->content->buildings_list = $buildings_list;

    }
    public function markers($space_id=null) {
        $space_id = preg_replace('/\.json$/i', '', $space_id);
        $space = null;
        if($space_id!==null) {
            $spaces = new Space_Model();
            $space = $spaces->get_markers($space_id);
        } else {

        }
        if ($this->is_json_request()) {
            $this->json_response($space);
        } else {
            $this->template->title = 'Seating::Spaces';
            $this->template->content = new View('pages/spaces');
        }
    }

    /**
     * This display the HTML/js page that will request markers/space_for
     * via JSONP, the response is the markers for that space
     *
     * @param string $email
     */
    public function with($target_marker_id=null) {
        isset($this->profiler)?$this->profiler->disable():null;
        $this->template = new View("space_for");
        $this->template->target_marker_id = $target_marker_id;
        $this->template->title = 'Seating::Spaces';
        $this->template->content = new View('pages/spaces');
        
    }

}
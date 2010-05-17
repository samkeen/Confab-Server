<?php defined('SYSPATH') or die('No direct script access.');

class Building_Model extends ORM {
    
    protected $belongs_to = array('site');

    public function validate(array & $array, $save = FALSE) {
        // Initialise the validation library and setup some rules
        $array = Validation::factory($array)
            ->pre_filter('trim')
            ->add_rules('name', 'required')
            ->add_rules('lat', 'valid::numeric')
            ->add_rules('long', 'valid::numeric')
            ->add_callbacks('lat',array($this,'validate_latlong_required_as_pair'))
            ->add_callbacks('long',array($this,'validate_latlong_required_as_pair'));
    
        return parent::validate($array, $save);
    }
    
    public function validate_latlong_required_as_pair(Validation $validation, $field) {
        // if we are already invalid, just return
        if (array_key_exists($field, $validation->errors())) {
            return;
        }
        $other_field = $field=='lat'?'long':'lat';
        if( ! empty($validation[$field]) && empty($validation[$other_field])) {
            $validation->add_error($other_field,"{$other_field} is required if {$field} is given");
        }
    }

}
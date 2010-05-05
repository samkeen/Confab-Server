<?php defined('SYSPATH') or die('No direct script access.');

class Controller extends Controller_Core {

  protected $url_extension = null;
  protected $json_callback = null;

  public function __construct() {
    parent::__construct();

    $this->profiler = new Profiler;



    $request_parts=pathinfo(url::current());
    $this->url_extension = isset($request_parts['extension'])?$request_parts['extension']:null;
    $this->json_callback = $this->input->get('callback');


  }
  public function is_json_request() {
    return request::is_ajax() || $this->url_extension=='json';
  }
  public function json_response($data, $echo=true) {
    isset($this->profiler)?$this->profiler->disable():null;

    $json_data = !is_resource($data)?json_encode($data):null;
    $json_data = $this->json_callback!==null
      ? "{$this->json_callback}({$json_data})"
      : $json_data;
//die($json_data);die;
    if($echo) {
      $this->auto_render = false;
      echo $json_data;
    } else {
      return $json_data;
    }
  }


}
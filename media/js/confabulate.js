//################## CONFAB ###########################################

var confab = {
  // 
  get_markers_url   : "http://seating.local/markers/space_for",
  save_markers_url  : "http://seating.local/markers/save",
  remove_marker_url : "http://seating.local/markers/remove",

  // the current makers indexed by id (primary key)
  current_markers : {},
  // defined in this.click_draw
  click_coords:null,
  // folder where interest images would be stoed locally [defualt to store on rsrd_server]
  interest_folder : '/media/img/spaces',

  current_focus_id : null,
  
  /**
   * all the aspects of location, so we have:
   * - present_location['site']     // ex: Mountain View
   * - present_location['building'] // ex: Main Office
   * - present_location['space']    // ex: 3rd Floor
   */
  present_location : {}, //
  
  // the canvas for the markers
  canvas_id : null,
  canvas_element : null,
  canv_context : null,
  // the left/top offset of the canvas on the page. Used to translate pointer clicks
  // to canvas clicks
  canvas_offset : {
    left:0,
    top:0
  },
  // take scrolling into account when translating window pointer position to
  // canvas pointer position.
  v_scroll_offset : 0,
  h_scroll_offset : 0,
  // flag used in dnd
  pointer_is_down : false,
  // the index of the marker in this.current_markers that the pointer is over (if any)
  over_marker_id : null,
  // the index of the marker in this.current_markers that we are dragging (if any)
  dragging_marker_id : null,

  list_highlight_color : 'orange',
  list_normal_color : '#EEE',
  canvas_default_draw_color : 'black',
  //font: bold italic large serif
  default_label_font : '900 12px Arial',

  icon:  '',

  //dialogs: {marker:,save_marker:null},

  init:function(canvas_id, current_focus_id) {

    this.icon = new Image();
    this.icon.src = '/media/img/icons/person.png';
    this.icon_highlight = new Image();
    this.icon_highlight.src = '/media/img/icons/person-highlight.png';

    $RTC_marker_dialog.init('seat_form',this.handle_marker_dialog_close);
    this.current_focus_id = typeof current_focus_id === "undefined"?null:current_focus_id;
    $RTC_log.debug("current_focus_id["+this.current_focus_id+"]");
    this.canvas_id = canvas_id;
    // retrieves and sets the markers to this.current_markers
    this.populate_markers();
  },
  remove_marker : function(marker_id) {
    var that = this;
    this.over_marker_id = null;
    $.getJSON(
      that.remove_marker_url+"/"+marker_id+".json?callback=?",
      function(result){
        if(result['success']) {
          $('#marker_feedback p').html("Removal Successful").fadeIn(1000).delay(1000).fadeOut(2000);
        }
        that.populate_markers();
      });
  },
  /**
     * Handles both Edit and Insert New
     * @param {} marker
     */
  save_marker : function(marker) {
    var that = this;
    var marker_id = marker.id!==null ? "/"+marker.id : "/";
    $.getJSON(
      that.save_markers_url+marker_id+".json?callback=?",
      that.square_brackify_keys('person', marker),
      function(result){
        if(result['success']) {
          $('#marker_feedback p')
          .html("Update Successful").fadeIn(1000).delay(1000).fadeOut(2000);
        }
        that.populate_markers();
      });
  },
  click_draw:function(click_event) {
    this.click_coords = this.get_canvas_coords_clicked(click_event);
    $RTC_log.info('click recorded at: ['+this.click_coords.canvas_x+','+this.click_coords.canvas_y+']');
    //      var coordinate_label = prompt('enter a label for this marker');
    //	var coordinate_description = prompt('enter a label for this marker');
    this.open_dialog('add');

  },
  /**
     * This is handed to the $RTC_marker_dialog init config as close_callback:
     *
     * @param state 'add'|'edit'|'delete'
     * @param all_fields (object) Keys are 'marker_label','marker_desc','marker_size','marker_color'
     * @param marker_id id of the marker the dialog was in refernce to
     *
     */
  handle_marker_dialog_close:function(state, all_fields, marker_id) {
    $RTC_log.debug('all fields',all_fields);
    switch (state) {
      case 'add':
        if(all_fields['marker_email']) {
          confab.save_marker({
            id: null,
            space_id: confab.present_location.space.id,
            email:all_fields['marker_email'],
            x:confab.click_coords.canvas_x,
            y:confab.click_coords.canvas_y
          });
        }
        break;
      case 'edit':
        if(all_fields['marker_email']) {
          confab.current_markers[marker_id].email = all_fields['marker_email'];
          confab.save_marker(confab.current_markers[marker_id]);
        }
        break;
      case 'delete':
        confab.remove_marker(marker_id);
        break;
      default:
        $RTC_log.debug('draw marker canceled');
        break;
    }
  },
  /**
     * this handles the state of the dialog before opening.
     *
     * @param state 'add'|'edit'
     * @param over_marker If we clicked on a marker, this is it.
     */
  open_dialog:function(state, over_marker) {
    var dialog_fields = null;
    var marker_id = null;
    if(state=='edit') {
      marker_id = over_marker.id;
      dialog_fields = {
        marker_email : over_marker.email
      }
    }
    $RTC_marker_dialog.open_dialog(state, dialog_fields, marker_id);
  },
  /*
     * translate the click on the screen coords relative to the canvas
     * origin.
     * The posision of this click will be considered the center of what-
     * ever we end up rendering
     */
  get_canvas_coords_clicked : function(click_event) {
    if (typeof(click_event)=="undefined")click_event=event;
    x_scroll_offset = window.pageXOffset!=='undefined'?window.pageXOffset:0;
    y_scroll_offset = window.pageYOffset!=='undefined'?window.pageYOffset:0;
    return {
      canvas_x:(click_event.clientX-this.canvas_offset.left+x_scroll_offset),
      canvas_y:(click_event.clientY-this.canvas_offset.top+y_scroll_offset)
    };
  },
  /*
   * determine the offset on the canvas element relative to the page.
   */
  calc_canvas_element_offset : function(){
    this.canvas_offset = $('#'+this.canvas_id).offset();
    this.canvas_offset.top = Math.floor(this.canvas_offset.top);
    this.canvas_offset.left = Math.floor(this.canvas_offset.left);
    $RTC_log.debug("canvas offset Top["+this.canvas_offset.top+"] Left["+this.canvas_offset.left+"]");
  },
  draw_current_markers:function() {
    this.canv_context.clearRect(0,0,this.canvas_element.width,this.canvas_element.height);
    for(var key in this.current_markers) {
      if (this.current_markers.hasOwnProperty(key) ) {
        this.draw_marker(this.current_markers[key], 'view');
      }
    }
  },
  
  paint_icon_label : function(marker) {
    var color = this.canvas_default_draw_color;
    this.canv_context.fillStyle = color;
    this.canv_context.textBaseline = "top";
    this.canv_context.textAlign = "center";
    this.canv_context.font = this.default_label_font;
    this.canv_context.fillText(marker.email, marker.x, Number(marker.y)+10);
  },
  paint_image_marker : function(marker, icon) {
    var img_h = Number(icon.height);
    var img_w = Number(icon.width);
    // get the coord's of top left corner'
    var draw_x = marker.x-(img_w/2);
    var draw_y = marker.y-(img_h/2);
    marker.delta_x = img_w;
    marker.delta_y = img_h;
    marker.bound_north = draw_y;
    marker.bound_south = draw_y+img_h;
    marker.bound_west = draw_x;
    marker.bound_east = draw_x+img_w;
    this.canv_context.drawImage(icon,draw_x,draw_y);
    return marker;
  },
  /*
   * @param {} marker {coordinate_label:_,click_x:_,click_y:_,}
   * @param mode 'add' | 'edit' | 'view'
   */
  draw_marker : function(marker, mode) {
    this.click_coords = null;
    marker = this.paint_image_marker(marker, this.icon);
    if(mode !== 'add') {
      if($('#marker_'+marker.id).length>0) {
        $('#marker_'+marker.id).replaceWith(this.marker_desc_list_element(marker));
      } else {
        $('#marker_descriptions').append(this.marker_desc_list_element(marker));
      }
      if(marker.focus) {
        // highlight it in the list
        $('#marker_'+marker.id).css('background-color', this.list_highlight_color);
        // repaint the marker using the hilight icon
        this.paint_image_marker(marker, this.icon_highlight);
        this.paint_icon_label(marker);
      }
    }
  },
  marker_desc_list_element : function(marker) {
    var that = this;
    var title = ' title="click here or on icon to edit" ';
    return $('<li id="marker_'+marker.id+'"'+title+'>'+marker.email+'</li>')
    .mouseover(function(event){
      var marker_id = $(this).attr('id').match(/\d+$/);
      that.focus_on(marker_id);
    })
    .mouseout(function(){
      var marker_id = $(this).attr('id').match(/\d+$/);
      that.focus_on(marker_id);
    })
    .click(function(){
      var marker_id = $(this).attr('id').match(/\d+$/);
      that.open_dialog('edit', that.current_markers[marker_id]);
    });
  },
  focus_on : function(focus_on_id) {
    for(var key in this.current_markers) {
      if (focus_on_id==key) {
        this.current_markers[key]['focus']=true;
      } else {
        this.current_markers[key]['focus']=false;
      }
    }
    this.draw_current_markers();
  },
  populate_markers : function() {
    var that = this;
    if(this.current_focus_id!==null) {
      $.getJSON(that.get_markers_url+"/"+this.current_focus_id+".json?callback=?",
        function(space_w_markers){
          var markers = space_w_markers['placements'];
          that.present_location.site = space_w_markers['site'];
          that.present_location.building = space_w_markers['building'];
          that.present_location.space = space_w_markers['space'];
          var image_path = that.present_location.space.img_uri.match(/^https?:\/\//)
            ? that.present_location.space.img_uri
            : that.interest_folder+'/'+that.present_location.space.img_uri;
          var image_height = null;
          var image_width = null;
          // clear the list
          $('#marker_descriptions li').remove();
          // place the title above the canvas
          $('#interest_details .site_name').html(that.present_location.site.name);
          $('#interest_details .building_name').html(that.present_location.building.name);
          $('#interest_details .space_name').html(that.present_location.space.name);
          that.current_markers = {};
          // place markers into a dictionary indexed by marker.id (its primary key)
          for(marker_key in markers) {
            that.current_markers[markers[marker_key].id] = markers[marker_key];
          }
          $RTC_log.debug("Backgroung Image Path:"+image_path);
          $('#source_image').attr('src', image_path);
          /*
		     * once the image loads, get its size and use that to create the
		     * canvas tag at the appropriate size.
		     */
          $('#source_image').load(function() {
            $('#source_image').removeAttr("width").removeAttr("height");
            image_height = $('#source_image').height();
            image_width = $('#source_image').width();
            $RTC_log.debug("Image Height:"+image_height);
            $RTC_log.debug("Image Width:"+image_width);
            // remove any previous canvas
            if ( $("#"+that.canvas_id).length > 0 ) {
              $("#"+that.canvas_id).remove();
            }
            $('#content').prepend('<canvas id="'+that.canvas_id+'" width="'+image_width+'" height="'+image_height+'" style="border:solid thin black;"></canvas>');
            // set the css image background of the canvas
            $('#'+that.canvas_id).css('background-image', "url('"+image_path+"')" );
            that.calc_canvas_element_offset();
            that.canvas_element = document.getElementById(that.canvas_id);
            that.canv_context = that.canvas_element.getContext('2d');
            // attach handlers
            that.canvas_element.onmouseup = function(event) {
              that.pointer_up_handler(event);
            }
            that.canvas_element.onmouseout = function(event) {
              that.pointer_out_handler(event);
            }
            that.canvas_element.onmousemove = function(event) {
              that.pointer_move_handler(event);
            }
            that.canvas_element.onmousedown = function(event) {
              that.pointer_down_handler(event);
            }
            that.draw_current_markers();

          });
        });
    } else {
      $RTC_log.error("making call to populate_markers but current_focus_id is not set. Aborting call");
    }
  },
  track_pointer_over_state : function(pointer_x, pointer_y) {
    // if we are over a marker watch that we don't leave it'
    if(this.over_marker_id != null) {
      if(this.pointer_left_marker_bounds(pointer_x, pointer_y)) {
        this.over_marker_highlighted=false;
        this.current_markers[this.over_marker_id]['focus']=false;
        this.draw_current_markers();
        $('#marker_'+this.over_marker_id).css('background-color', '#eee');
        this.over_marker_id = null;
      }
    } else { // for each marker see if we are over it
      this.over_marker_id = this.pointer_over_marker_id(pointer_x, pointer_y);
    }
    if(this.over_marker_id!=null && !this.over_marker_highlighted) {
      this.over_marker_highlighted=true;
      this.focus_on(this.over_marker_id);
      $('#marker_'+this.over_marker_id).css('background-color', this.list_highlight_color);
      $RTC_log.debug('Over Marker',this.current_markers[this.over_marker_id]);
    }
  },
  pointer_left_marker_bounds : function(pointer_x, pointer_y) {
    var over_marker = this.current_markers[this.over_marker_id];
    // shift the screen x/y to the relative to canvas x/y
    pointer_x = pointer_x - this.canvas_offset.left;
    pointer_y = pointer_y - this.canvas_offset.top;
    return	pointer_y < over_marker.bound_north
    ||  pointer_y > over_marker.bound_south
    ||  pointer_x < over_marker.bound_west
    ||  pointer_x > over_marker.bound_east;
  },
  pointer_over_marker_id : function(pointer_x, pointer_y) {
    var over_marker = null;
    // shift the screen x/y to the relative to canvas x/y
    pointer_x = pointer_x - this.canvas_offset.left;
    pointer_y = pointer_y - this.canvas_offset.top;
    for(key in this.current_markers) {
      if(     pointer_y > this.current_markers[key].bound_north
        &&  pointer_y < this.current_markers[key].bound_south
        &&  pointer_x > this.current_markers[key].bound_west
        &&  pointer_x < this.current_markers[key].bound_east) {
        over_marker = this.current_markers[key];
        break;
      }
    }
    return over_marker!==null?over_marker.id:null;
  },
  square_brackify_keys : function(key_name, obj) {
    var brackified = {};
    for(key in obj) {
      brackified[key_name+'['+key+']'] = obj[key];
    }
    return brackified;
  },
  pointer_move_handler : function(event) {
    if(this.pointer_is_down && !this.dragging_marker_id) {
      $RTC_log.debug("MOVE START, dragging Marker:", this.current_markers[this.over_marker_id]);
      this.dragging_marker_id = this.over_marker_id;
    }
    this.track_pointer_over_state(event.pageX,event.pageY);
    var canvas_coords = null;
    if(this.dragging_marker_id) {
      canvas_coords = this.get_canvas_coords_clicked(event);
      this.current_markers[this.dragging_marker_id].x = canvas_coords.canvas_x;
      this.current_markers[this.dragging_marker_id].y = canvas_coords.canvas_y;
      this.draw_current_markers();
    }
  },
  pointer_up_handler : function(event) {
    this.pointer_is_down = false;
    if(this.dragging_marker_id) { // finishing a click and drag
      $RTC_log.debug('DRAG STOP:, this.dragging_marker_id:', this.dragging_marker_id);
      this.save_marker(this.current_markers[this.dragging_marker_id]);
      this.dragging_marker_id = null;
    } else {// just a quick click down/up so we are creating a new marker or editing an existing one
      if(this.over_marker_id!=null) {
        $RTC_log.debug('click over: "'+this.current_markers[this.over_marker_id].coordinate_label+'"');
        this.open_dialog('edit', this.current_markers[this.over_marker_id]);
      } else { // draw new marker
        this.click_draw(event);
      }
    }
    $RTC_log.debug('Pointer Up Fired:', event);
  },
  pointer_down_handler : function(event) {
    this.pointer_is_down = true;
  },
  pointer_out_handler : function(event) {

  }
};


//################## MARKER DIALOG ###########################################

var $RTC_marker_dialog = {

  form_profile : null,
  inputs_lookup:{},
  form_inputs:[],
  feedback_element:null,
  close_callback:null,
  button_clicked: false,

  init:function(profile_name, close_callback) {
    var that = this;
    this.form_profile = this.form_profiles[profile_name]||null;
    if(this.form_profile){
      this.close_callback=close_callback;
      if(!this.form_profile.dialog_id||!this.form_profile.feedback_element_id||!this.form_profile.form_input_ids||!this.close_callback) {
        $RTC_log.error("Missing required args for $RTC_marker_dialog.init", $RTC_marker_dialog);
      }
      $(this.form_profile.form_input_ids).each(function(index,item){
        that.inputs_lookup[item] = $('#'+item);
        $RTC_log.debug($('#'+item));
        that.form_inputs.push(that.inputs_lookup[item]);
      });
      that.feedback_element=$("#"+this.form_profile.feedback_element_id);
      $RTC_log.debug(that.form_inputs);

      $("#"+this.form_profile.dialog_id).dialog(this.form_profile.chrome);

      $('#create-user').click(function() {
        $('#dialog').dialog('open');
      })
      .hover(
        function(){
          $(this).addClass("ui-state-hover");
        },
        function(){
          $(this).removeClass("ui-state-hover");
        }
        ).mousedown(function(){
        $(this).addClass("ui-state-active");
      })
      .mouseup(function(){
        $(this).removeClass("ui-state-active");
      });

    } else {
      $RTC_log.error('Form Profile not known: ['+this.form_profile+']')
    }

  },
  open_dialog : function(state, dialog_fields, marker_id) {
    if(state=='edit') {
      this.button_state('Remove', 'show');
      this.set_field_values(dialog_fields);
    } else { // is ADD
      this.button_state('Remove', 'hide');
    }
    // set the 'state' hidden field: add|edit (to be recovered on close)
    if($('#'+this.form_profile.dialog_id+' form #state').length) {
      $('#'+this.form_profile.dialog_id+' form #state').val(state);
    } else {
      $('#'+this.form_profile.dialog_id+' form').append('<input type="hidden" id="state" value="'+state+'" />');
    }
    // set the marker_id hidden field (to be recovered on close)
    if(marker_id) {
      if($('#'+this.form_profile.dialog_id+' form #marker_id').length) {
        $('#'+this.form_profile.dialog_id+' form #marker_id').val(marker_id);
      } else {
        $('#'+this.form_profile.dialog_id+' form').append('<input type="hidden" id="marker_id" value="'+marker_id+'" />');
      }
    }
    $('#'+this.form_profile.dialog_id).dialog('open');
  },
  clear_form:function() {
    $($RTC_marker_dialog.form_inputs).each(function(){
      $(this).val('').removeClass('ui-state-error');
    });
  },
  clear_form_errors:function() {
    $($RTC_marker_dialog.form_inputs).each(function(){
      $(this).removeClass('ui-state-error');
    });
  },
  all_field_values:function() {
    var all_values = {};
    $($RTC_marker_dialog.form_inputs).each(function(){
      all_values[$(this).attr('id')]=$(this).val();
    });
    return all_values;
  },
  /**
     * @param object field_values {(field id):(field value),...}
     */
  set_field_values:function(submitted_input) {
    if(submitted_input) {
      $(this.form_inputs).each(function(i, input){
        input.val(submitted_input[input.attr('id')]);
      });
    }
  },
  updateTips:function(t) {
    this.feedback_element.text(t).effect("highlight",{},1500);
  },

  checkLength:function(input_element,msg_label,min,max) {
    if ( input_element.val().length > max || input_element.val().length < min ) {
      input_element.addClass('ui-state-error');
      this.updateTips("Length of " + msg_label + " must be between "+min+" and "+max+".");
      return false;
    } else {
      return true;
    }
  },
  required:function(input_element, msg_label) {
    if ( !input_element.val().length > 0 ) {
      input_element.addClass('ui-state-error');
      this.updateTips(msg_label + " is Required");
      return false;
    } else {
      return true;
    }
  },
  email:function(input_element,error_message) {
    var valid = false;
    var pattern = /^\s*[\w\-\+_]+(\.[\w\-\+_]+)*\@[\w\-\+_]+\.[\w\-\+_]+(\.[\w\-\+_]+)*\s*$/;
    valid = String(input_element.val()).search (pattern) != -1;
    if ( !valid ) {
      input_element.addClass('ui-state-error');
      this.updateTips(error_message);
    }
    return valid;
  },
  checkRegexp:function(input_element,regexp,error_message) {
    if ( !( regexp.test( input_element.val() ) ) ) {
      input_element.addClass('ui-state-error');
      this.updateTips(error_message);
      return false;
    } else {
      return true;
    }
  },
  /**
     * @param button_label (only way to get ahold of a button in jqui)
     * @param state show|hide
     */
  button_state : function(button_label, state) {
    if(state=='show') {
      $(":button:contains("+button_label+")").show();
    } else if(state=='hide') {
      $(":button:contains("+button_label+")").hide();
    }
  },

  form_profiles : {

    text_form : {
      'dialog_id' : 'marker_dialog',
      'form_input_ids' : ['marker_label','use_icon','marker_desc','marker_size','marker_color'],
      'feedback_element_id' : 'validateTips',
      'chrome' : {
        autoOpen: false,
        height: 400,
        width: 400,
        modal: true,
        buttons: {
          'Create Marker': function() {
            that.clear_form_errors();
            var bValid = true;
            $(that.form_inputs).removeClass('ui-state-error');
            bValid = bValid && that.checkLength(that.marker_label,"Label",1,50);
            //bValid = bValid && $RTC_marker_dialog.checkRegexp($RTC_marker_dialog.marker_size,/^[0-9]+\.?[0-9]*$/,"The marker size should be numeric");
            if (bValid) {
              $(this).dialog('close');
            }
          },
          Cancel: function() {
            that.clear_form();
            $(this).dialog('close');
          },
          'Delete Marker' : function() {
            $(this).dialog('close');
          }
        }
        ,
        close: function(event, ui) {
          var state = $(this).find('form #state').val();
          // report values back to confab
          $RTC_log.debug($RTC_marker_dialog);
          if($RTC_marker_dialog.button_clicked && state!='cancel') {
            $RTC_marker_dialog.close_callback(state, $RTC_marker_dialog.all_field_values());
          }
          //confab.retrieve_dialog_values($RTC_marker_dialog.all_field_values());
          $RTC_marker_dialog.clear_form();
        }
      }
    },

    seat_form : {
      'dialog_id' : 'seat_dialog',
      'feedback_element_id' : 'validateTips',
      'form_input_ids' : ['marker_email'],
      'chrome' : {
        autoOpen: false,
        height: 200,
        width: 400,
        modal: true,
        buttons: {
          Cancel: function() {
            $RTC_marker_dialog.button_clicked = true;
            $RTC_marker_dialog.clear_form();
            $(this).find('form #state').val('cancel');
            $(this).dialog('close');
          },
          Save: function() {
            $RTC_marker_dialog.button_clicked = true;
            $RTC_marker_dialog.clear_form_errors();
            var bValid = true;
            $($RTC_marker_dialog.form_inputs).removeClass('ui-state-error');
            bValid = bValid && $RTC_marker_dialog.email($RTC_marker_dialog.inputs_lookup['marker_email'],"Not a valid Email");
            if (bValid) {
              $(this).dialog('close');
            }
          },
          Remove: function() {
            $RTC_marker_dialog.button_clicked = true;
            $(this).find('form #state').val('delete');
            $(this).dialog('close');
          }
        },
        close: function(event) {
          var state = $(this).find('form #state').val();
          var marker_id = $(this).find('form #marker_id').val();
          // report values back to confab
          $RTC_log.debug($RTC_marker_dialog);
          if($RTC_marker_dialog.button_clicked && state!='cancel') {
            $RTC_marker_dialog.close_callback(state, $RTC_marker_dialog.all_field_values(), marker_id);
          }
          $RTC_marker_dialog.clear_form();
        }
      }

    }
  }

}
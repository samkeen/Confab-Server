/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
var edit_in_place = {
    /*
     * {id:...,target_url:...}
     */
    editable : function(editable_element_id) {
	$('#'+editable_element_id)
	    .append($('<a id="interest_edit_'+id+'" href="">edit</a>')
	    .click(function(){
		edit_in_place.input_element($(this).attr('id'), $(this).prev().html());
		return false;
	    })
	);
    },
    label_element : function(id, label) {
	return  $('<li class="editable" id="interest_edit_'+id+'"><a href="interest.php?id='+id+'">'+label+'</a></li>')
	    .append($('<a id="interest_edit_'+id+'" href="">edit</a>')
	    .click(function(){
		edit_in_place.input_element($(this).attr('id'), $(this).prev().html());
		return false;
	    })
	);
    },
    input_element : function(element_id, label) {
	var id = element_id.match(/\d+$/);
	$('#'+element_id).replaceWith($('<li  class="editable" id="'+element_id+'"></li>')
	    .append($('<input class="input_edit" rel="'+label+'" id="input_'+id+'" value="'+label+'" type="text"></input>')
	    .keyup(function(e) {
		if(e.keyCode == 13) {
		    edit_in_place.edit_interest($(this).attr('id').match(/\d+$/),$(this).val());
		}
	    })
	    )
	    .append($('<a class="cancel_edit" href="">cancel</a>')
		.click(function(){
		    $('#interest_edit_'+id).replaceWith(edit_in_place.label_element(id,label));
		    return false;
		})
	    )
	);
    },
    edit_interest : function(current_interest_id, new_name) {
	// shift coordinates relative to 0,0 origin
	$.getJSON(
	    confab.rcrd_server+"/interest/"+current_interest_id+".json?_method=edit&jsoncallback=?",
	    confab.square_brackify_keys('interest',{name: new_name}),
	    function(edited_interest, text_status){
		var updated_interest = edited_interest.pop();
		if(updated_interest!='undefined'&&updated_interest!=null) {
		    $('#interest_edit_'+updated_interest.id).replaceWith(edit_in_place.label_element(updated_interest.id, updated_interest.name));
		} else {

		}
	    }

	);
    }

};
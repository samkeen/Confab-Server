/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
//################## CANVAS UTILS ###########################################

var $RTC_canvas_util = {
  /**
     *
     *
     */
  find_text_bounds:function(canvas_element, seeking_rgba) {
    $RTC_log.info('$RTC_canvas_util.find_bounds(seeking_rgba): seeking_rgba:',seeking_rgba);
    var coords = {
      north_bound: 99999,
      south_bound: -99999,
      west_bound: 99999,
      east_bound: -99999
    }
    var target_channels = {
      red:null,
      green:null,
      blue:null,
      alpha:null
    };
    var canvas_data = canvas_element.getContext('2d').getImageData(
      0, 0, canvas_element.width, canvas_element.height
      );
    var bounds_found = false;
    for (var x = 0; x < canvas_data.width; x++) {
      for (var y = 0; y < canvas_data.height; y++) {
        var idx = (x + y * canvas_element.width) * 4;
        // The RGB values
        target_channels.red = canvas_data.data[idx + 0];
        target_channels.green = canvas_data.data[idx + 1];
        target_channels.blue = canvas_data.data[idx + 2];
        target_channels.alpha = canvas_data.data[idx + 3];
        if(target_channels.red==255&&target_channels.green==null&&target_channels.blue==null){
          $RTC_log.debug("ALPHA   ",target_channels.alpha)
        };
        /*
                 * examine each channel, if it is at the value we are seeking, update
                 * n,s,w,e
                 */
        for (var color_channel in target_channels) {
          if(seeking_rgba[color_channel]!=undefined) {
            if(target_channels[color_channel]==seeking_rgba[color_channel]) {
              bounds_found = true;
            } else {
              bounds_found = false;
              break;
            }
          }
        }
        if(bounds_found) {
          $RTC_log.debug('found  @ x:'+x+', y:'+y);
          coords.north_bound = Math.min(coords.north_bound,y);
          coords.south_bound = Math.max(coords.south_bound,y);
          coords.west_bound = Math.min(coords.west_bound,x);
          coords.east_bound = Math.max(coords.east_bound,x);
          bounds_found = false;
        }
      }
    }
    return coords.north_bound<99999
    ? {
      found:true,
      north:coords.north_bound,
      south:coords.south_bound,
      west:coords.west_bound,
      east:coords.east_bound
    }
    : {
      found:false,
      north:null,
      south:null,
      west:null,
      east:null
    };
  },
  debug_highlight : function(context, marker, color) {
    color = color || "red";
    context.beginPath();
    context.moveTo(marker.bound_west, marker.bound_north);
    context.lineTo(marker.bound_east, marker.bound_north);

    context.moveTo(marker.bound_east, marker.bound_north);
    context.lineTo(marker.bound_east, marker.bound_south);

    context.moveTo(marker.bound_east, marker.bound_south);
    context.lineTo(marker.bound_west, marker.bound_south);

    context.moveTo(marker.bound_west, marker.bound_south);
    context.lineTo(marker.bound_west, marker.bound_north);

    context.strokeStyle = color;
    context.stroke();
  }
}


<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <title>Test Confabulate Document</title>
    <!--CSS Foundation: (also partially aggegrated in reset-fonts-grids.css; does not include base.css)-->
    <link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.7.0/build/reset/reset-min.css" />
    <link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.7.0/build/base/base-min.css" />
    <link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.7.0/build/fonts/fonts-min.css" />
    <link type="text/css" href="/media/css/ui-lightness/jquery-ui-1.7.2.custom.css" rel="stylesheet" />
    <link type="text/css" href="/media/css/style.css" rel="stylesheet" />
  </head>

  <body>
    <div id="header">
      <h1>Confabulate</h1>
    </div>
    <div id="body">
      <div id="content">
    <!--<canvas id="the-canvas" width="500" height="500" style="border:solid thin black;"></canvas>-->
        <img id="source_image" src="" />
        <div id="interest_details">
          <h3 id="title"></h3>
          <ul id="marker_descriptions"></ul>
        </div>

      </div>
      <div id="sub-content">
        <p><a href="/" >Back to List</a></p>
        <div id="server_feedback" ></div>
      </div>
    </div>
    <div id="footer">

    </div>

    <div id="seat_dialog" title="">
      <p id="validateTips"></p>
      <form id="text_form" action="">

        <fieldset><legend></legend>
          <label for="marker_email">email</label>
          <input type="text" name="marker_email" id="marker_email" class="text ui-widget-content ui-corner-all" />
        </fieldset>
      </form>
    </div>
    <div id="marker_dialog" title="Create new user" style="display: none">
      <p id="validateTips"></p>
      <form id="text_form" action="">

        <fieldset><legend></legend>
          <label for="marker_label">Label</label>
          <input type="text" name="marker_label" id="marker_label" class="text ui-widget-content ui-corner-all" />
          <label for="marker_label">Use Icon</label>
          <input type="checkbox" name="use_icon" id="use_icon" class="ui-widget-content ui-corner-all" />
          <label for="marker_desc">Description</label>
          <textarea name="marker_desc" id="marker_desc" value="" class="text ui-widget-content ui-corner-all" ></textarea>
          <label for="marker_size">label font size</label>
          <input type="text" name="marker_size" id="marker_size" value="" class="text ui-widget-content ui-corner-all" />
          <label for="marker_color">label color</label>
          <input type="text" name="marker_color" id="marker_color" value="" class="text ui-widget-content ui-corner-all" />

        </fieldset>
      </form>
    </div>
    <script type="text/javascript" src="http://www.google.com/jsapi?key=ABQIAAAATo3-ybCesv4eEUL9uVlUvRSO7clIrqm6tocdV4EDJyMUpPvCThQTEcgc1BZfRNwelXlIK4B7Oe4raQ"></script>
    <script type="text/javascript">
      google.load("jquery", "1.3.2");
      google.load("jqueryui", "1.7.2");
    </script>
    <script type="text/javascript" src="/media/js/confabulate.js" charset="utf8"></script>
    <script type="text/javascript">
      $(document).ready(function(){
        var interest_id=window.location.search.match(/id=([@\w\.]+)/);
        confab.init('the-canvas',interest_id!==null?interest_id[1]:null);
      });
    </script>
  </body>
</html>
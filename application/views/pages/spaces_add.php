<?php echo form::open(); ?>

<div>
<?php
echo form::label('name');
echo form::input('name', $form['name']);
client::validation('name');
?>
</div>

<div>
<?php 
echo form::label('building_id');
echo form::dropdown('building_id',$buildings_list,$form['building_id']);
client::validation('building_id');
?>
</div>

<div>
<?php
echo form::label('index');
echo form::input('index', $form['index']);
client::validation('index');
?>
</div>

<div>
<?php
echo form::label('img_uri');
echo form::input('img_uri', $form['img_uri']);
client::validation('img_uri');
?>
</div>

<div>
<?php
echo form::label('active');
echo form::checkbox('active','1', $form['active']=='1');
?>
</div>


<?php echo form::close(); ?>
<h2>Sites :: Add</h2>

<?php echo form::open(); ?>
<p>
<?php
echo form::auto_label('name');
echo form::input('name', $site->name, 'size="20"');
client::validation('name');
?>
</p>

<p>
<?php
echo form::auto_label('lat');
echo form::input('lat', $site->lat, 'size="20"');
client::validation('lat');
?>
</p>

<p>
<?php
echo form::auto_label('long');
echo form::input('long', $site->long, 'size="20"');
client::validation('long');
?>
</p>

<p>
<?php
echo form::submit('submit', 'Save');
echo form::close();
?>
</p>
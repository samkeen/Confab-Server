<h2>Sites</h2>
<ul>
<?php foreach ($sites as $site)  {
    $buildings = $site->buildings; ?>
    <li>
        <span class="name"><?php echo html::specialchars($site->name); ?></span>
        <?php echo html::anchor("sites/edit/{$site->id}","Edit"); ?>
        <?php echo html::anchor("sites/delete/{$site->id}","Delete"); ?>
        <?php if($buildings) { ?>
            <ul>
                <?php foreach($buildings as $building) { ?>
                <li>
                    <span class="name"><?php echo html::specialchars($building->name); ?></span>
                    <?php echo html::anchor("buildings/edit/{$building->id}","Edit"); ?>
                    <?php echo html::anchor("buildings/delete/{$building->id}","Delete"); ?>
                </li>
                <?php } ?>
            </ul>
        <?php } ?>
    </li>
<?php } ?>
</ul>
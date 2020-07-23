<div class="<?php echo $data->atts['container_id']; ?>">
<?php
use MZ_Mobilize_America as NS;
?>
<?php echo $data->api_object->get_navigation(); ?>
<table class="">
<?php

foreach($data->api_object->request_results->data as $k => $org){ ?>
    <tr><td><?php echo $org->id; ?></td><td><?php echo $org->name; ?></td>
<?php } ?>

</table>
<?php echo $data->api_object->get_navigation(); ?>
</div>
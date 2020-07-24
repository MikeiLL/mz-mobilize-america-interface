<div class="<?php echo $data->atts['container_id']; ?>">
<?php
use MZ_Mobilize_America as NS;
// echo "<pre>";
// print_r($data->api_object);
// echo "</pre>";
?>
<span><?php echo $data->api_object->display_segment_info(); ?></span>
<span><?php echo $data->api_object->display_pagination_info(); ?></span>
<?php echo $data->api_object->get_numeric_navigation(); ?>
<?php echo $data->api_object->get_step_navigation(); ?>
<table class="">
<?php

foreach($data->api_object->request_results->data as $k => $org){ ?>
    <tr><td><?php echo $org->id; ?></td><td><?php echo $org->name; ?></td>
<?php } ?>

</table>
<?php echo $data->api_object->get_step_navigation(); ?>
</div>
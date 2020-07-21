<?php
use MZ_Mobilize_America\Common as Common;

?><div id="<?php echo $data->container_id; ?>" class="<?php echo $data->container_class; ?>"><?php echo $data->loading_text; ?></div>
<div style="color:#f04">
<?php


//$template_loader = new Libraries\Template_Loader();
//$template_loader->set_template_data( ['atts' => $this->atts, 'api_result' => $api_result] );
//$template_loader->get_template_part( $this->atts['endpoint'] );
        
foreach($data->api_result->data as $k => $event){
    ?>
        <h3><?php echo $event->title; ?></h3>
        <ul>
        <?php foreach($event->timeslots as $timeslot) { ?>
            <li><?php echo date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $timeslot->start_date); ?> - <?php echo date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $timeslot->end_date); ?></li>
        <?php } ?>
    </ul>
    <?php
}
?>
</div>

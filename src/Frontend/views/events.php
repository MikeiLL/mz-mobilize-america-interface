<?php
use MZ_Mobilize_America\Common as Common;
use MZ_Mobilize_America\Libraries as Libraries;

?>
<div class="<?php echo $data->atts['container_id']; ?>">

<span><?php echo $data->api_object->display_segment_info(); ?></span>
<span><?php echo $data->api_object->display_pagination_info(); ?></span>
<?php echo $data->api_object->get_numeric_navigation(); ?>
<?php echo $data->api_object->get_step_navigation(); ?>
<?php

foreach($data->api_object->request_results->data as $k => $event){ ?>
    <h5><?php echo $event->title; ?></h5>
    <p><?php echo $event->description; ?></p>
    <?php if (isset($event->venue)): ?>
        <h6><?php echo $event->venue ?></h6>
        <?php endif; ?>
        <h6><?php echo $event->location_address . ' ' . $event->locality . ' ' . $event->region . ' ' . $event->postal_code ?></h6>
        <?php
        $event_count = count($event->timeslots);
        if ($event_count > 3):
          ?>
          <div>
            <em><?php echo $event_count; ?> events between <?php echo get_date_from_gmt(date('Y-m-d H:i:s', $event->timeslots[0]->start_date),'F j'); ?>
            and <?php echo get_date_from_gmt(date('Y-m-d H:i:s', end($event->timeslots)->start_date),'F j'); ?>.</em>
          </div>
          <?php
        else: ?>
        <ul>
        <?php
         foreach ($event->timeslots as $k => $time_slot):
            // Don't display event if time is previous to now.
            // if ($time_slot->end_date <= current_time('timestamp')) continue;
        ?>
        <li><?php echo get_date_from_gmt(date('Y-m-d H:i:s', $time_slot->start_date),'l, F j g:i a') ?> -
        <?php echo get_date_from_gmt(date('Y-m-d H:i:s', $time_slot->end_date), 'g:i a') ?>
        </li>
        <?php endforeach; ?>
        </ul>
        <?php endif; ?>
    <a class="<?php echo $data->atts['button_class']; ?>" href="<?php echo $event->browser_url; ?>"><?php echo $data->atts['sign_up_text']; ?></a> |
    <a class="<?php echo $data->atts['button_class']; ?>" href="<?php echo $event->sponsor->event_feed_url; ?>"><?php echo $data->atts['full_listing_text']; ?></a>
<?php } ?>

<?php echo $data->api_object->get_step_navigation(); ?>
</div>

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
  <?php
  $css_classes = "event_type_" . $event->event_type . ' ';
  $css_classes .= "visibility_" . $event->visibility . ' ';
  $css_classes .= "accessibility_status_" . $event->accessibility_status . ' ';
  $css_classes .= "approval_status_" . $event->approval_status . ' ';
  ?>
  <div class="<?php echo $css_classes?>">
    <h5><?php echo $event->title; ?></h5>
    <p>
    <?php if ( ($data->atts['thumbnail'] != false) && !empty($event->featured_image_url) ): ?>
    <img src="<?php echo $event->featured_image_url; ?>" class="mobilize-event-image alignright">
    <?php endif; ?>
    <?php echo $event->description; ?>
    </p>
    <?php if (isset($event->venue)): ?>
        <h6><?php echo $event->venue ?></h6>
        <?php endif; ?>
        <h6><?php echo (isset($event->location_address) ? $event->location_address : '')
          . ' ' . (isset($event->locality) ? $event->locality : '')
          . ' ' . (isset($event->region) ? $event->region : '')
          . ' ' . (isset($event->postal_code) ? $event->postal_code : '')?></h6>
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
  </div>
<?php } ?>

<?php echo $data->api_object->get_step_navigation(); ?>
</div>

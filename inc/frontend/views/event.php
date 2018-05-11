<div class="mobilize-america-event">
    <?php
    $info_container_class = '';
    if ( $data->thumbnail && !empty($data->featured_image_url) ):
        $info_container_class = 'col-md-8';
    ?>
        <div class="col-md-4 event_image" style="background:url('<?php echo $data->featured_image_url; ?>') no-repeat;background-size:contain;">
        </div>
    <?php endif; ?>
    <div class="event_details <?php echo $info_container_class; ?>">
        <h5><?php echo $data->title; ?></h5>
        <?php if (isset($data->venue)): ?>
        <h6><?php echo $data->venue ?></h6>
        <?php endif; ?>
        <h6><?php echo $data->location_address . ' ' . $data->locality . ' ' . $data->region . ' ' . $data->postal_code ?></h6>
        <?php
         foreach ($data->time_slots as $k => $time_slot):
            // Don't display event if time is previous to now.
            if ($time_slot->end_date <= current_time('timestamp')) continue;
        ?>
        <span><?php echo get_date_from_gmt(date('l, F j g:i a', $time_slot->start_date),'l, F j g:i a') ?> -
        <?php echo get_date_from_gmt(date('g:i a', $time_slot->end_date), 'g:i a') ?>
        </span><br/>

        <? endforeach; ?>
        <br/>
        <a class="btn-event float-right" href="<?php echo $data->url; ?>">Sign Up</a>
    </div>
</div>

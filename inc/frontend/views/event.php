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
        <span><?php echo $data->start_date ?> - <?php echo $data->end_time ?></span>
        <br/>
        <a class="btn-event float-right" href="<?php echo $data->url; ?>">Sign Up</a>
    </div>
</div>

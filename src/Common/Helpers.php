<?php
namespace MZ_Mobilize_America\Common;

class Helpers {
    /*
     *
     *
     */
    public static function sequence_events($events_data) {
        // Break each event into multiple events if
        $all_events = [];
        print_r($events_data);
        foreach ($events_data as $k => $event_data)
            usort($event_data, function( $a, $b ) {
                    return $a->timeslots[0]->start_date - $b->timeslots[0]->start_date;
                });
        }
        return $event_data;
    }
}
?>
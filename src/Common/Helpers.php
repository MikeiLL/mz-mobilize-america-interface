<?php
namespace MZ_Mobilize_America\Common;

class Helpers {

    /*
     * Sequence Events
     * 
     * @since 1.0.0
     * @not in use, currently. May need later.
     *
     */
    public static function sequence_events($events_data) {
        // Break each event into multiple events if
        $all_events = [];

        foreach ($events_data as $k => $event_data){
            usort($event_data, function( $a, $b ) {
                    return $a->timeslots[0]->start_date - $b->timeslots[0]->start_date;
                });
        }
        return $event_data;
    }

    /*
     * Add query vars
     * 
     * @since 1.0.0
     * @hooked by query_vars filter
     * @param $vars allowed wp query variables
     * @return wp query vars plus ours
     */
    public function add_query_vars( $vars ){
        $vars[] = "mobilize_page";
        return $vars;
    }
}
?>
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
    
    
    /**
     * Helper function to write strings or arrays to the screen
     *
     * @since     1.0.1
     *
     * @param $message the content to be written to screen.
     */
    public function print($message = '')
    {
        echo "<pre>";
        print_r($message);
        echo "</pre>";
    }

    /**
     * Helper function to write strings or arrays to a file
     *
     * @since     1.0.1
     *
     * @param $message the content to be written to file.
     * @param $file_path string optional path to write file to.
     */
    public function log($message, $file_path='')
    {
        $file_path = ( ($file_path == '') || !file_exists($file_path) ) ? WP_CONTENT_DIR . '/mobilize_america.log' : $file_path;
        $header = date('l dS \o\f F Y h:i:s A', strtotime("now")) . "\t ";

        // Just keep up to seven days worth of data
        if (file_exists($file_path)){
            if (time() - filemtime($file_path) >= 60 * 60 * 24 * 7) { // 7 days
                unlink($file_path);
            }
        }

        if (is_array($message) || is_object($message)) {
            $message = print_r($message, true);
        }
        $message .= "\n";
        file_put_contents(
            $file_path,
            $header . $message,
            FILE_APPEND | LOCK_EX
        );
    }
}
?>
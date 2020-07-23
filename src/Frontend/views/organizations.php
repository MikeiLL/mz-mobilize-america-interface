List of Organizations:
<pre>
<?php

// Build up link for next and previous pages
$next_page = wp_parse_args(wp_parse_url($data->api_result->next)['query'])['page'];
$previous_page = wp_parse_args(wp_parse_url($data->api_result->previous)['query'])['page'];
$next = add_query_arg('mobilize_page', $next_page, get_the_permalink());
$prev = add_query_arg('mobilize_page', $prev, get_the_permalink());

//print_r($next);
echo "<hr>";
//print_r(wp_parse_args(wp_parse_url($data->api_result->previous))['query']);
//print_r($data->api_result);

function get_mobilize_page_query($url_string){
    $url_array = wp_parse_url($url_string);
    $query_args = wp_parse_args($url_array['query']);
    return $query_args['page'];
}
print_r(get_mobilize_page_query($data->api_result->next));
?>
</pre>
<table>
<?php

foreach($data->api_result->data as $k => $org){ ?>
    <tr><td><?php echo $org->id; ?></td><td><?php echo $org->name; ?></td>
<?php } ?>

</table>
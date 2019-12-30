<?php

function custom_dashboard_widget_callback() {
?>
    <p>Book post type books</p>
<?php
    $args = array(         
                'post_type' => 'book',
                'tax_query' => array(
                    'relation' => 'OR',
                    array(
                        'taxonomy' => 'genre',
                        'field'    => 'term_id',
                        'terms'    => array( 4, 5 ),
                    ),
                ),
            );
    $query1 = new WP_Query( $args );
        
    if( $query1->have_posts() ) {
        while( $query1->have_posts() ) {
            $query1->the_post(); ?>                
            <?php the_title( '<h4 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h4>' ); ?>    
        <?php    

        }
    }
}

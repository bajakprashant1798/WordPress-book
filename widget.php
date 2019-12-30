<?php

class Category_Posts extends WP_Widget 
{
    public function __construct() 
    {
        parent::__construct(
            'widget_category_posts', 
            _x( 'Category Posts Widget', 'Category Posts Widget' ), 
            [ 'description' => __( 'Display a list of posts from a selected category.' ) ] 
        );
        $this->alt_option_name = 'widget_category_posts';

        add_action( 'save_post', [$this, 'flush_widget_cache'] );
        add_action( 'deleted_post', [$this, 'flush_widget_cache'] );
        add_action( 'switch_theme', [$this, 'flush_widget_cache'] );
    }

    public function widget( $args, $instance ) 
    {
        $cache = [];
        if ( ! $this->is_preview() ) {
            $cache = wp_cache_get( 'widget_cat_posts', 'widget' );
        }

        if ( ! is_array( $cache ) ) {
            $cache = [];
        }

        if ( ! isset( $args['widget_id'] ) ) {
            $args['widget_id'] = $this->id;
        }

        if ( isset( $cache[ $args['widget_id'] ] ) ) {
            echo $cache[ $args['widget_id'] ];
            return;
        }

        ob_start();
        
        $title          = ( ! empty( $instance['title'] ) ) ? $instance['title'] : __( 'Category Posts' );
        /** This filter is documented in wp-includes/default-widgets.php */
        $title          = apply_filters( 'widget_title', $title, $instance, $this->id_base );
        $number         = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 5;
        if ( ! $number ) {
            $number = 5;
        }
        $cat_id         = $instance['cat_id'];
        $random         = $instance['rand'] ? true : false; 
        $excerpt        = $instance['excerpt'] ? true : false; 
     
        if( $instance['cat_id'] === 4 ) {

            $query_args = array(         
                'post_type' => 'book',
                'tax_query' => array(
                    'relation' => 'OR',
                    array(
                        'taxonomy' => 'genre',
                        'field'    => 'term_id',
                        'terms'    => array( 4 ),
                    ),
                ),
            );
            
        }else{  

            $query_args = array(
                'post_type' => 'book',
                'order'     => 'ASC',
                'tax_query' => array(
                    'relation' => 'OR',
                    array(
                        'taxonomy' => 'genre',
                        'field'    => 'term_id',
                        'terms'    => array( 5 ),
                    ),
                ),
            );
            
        }
        $query = new WP_Query( $query_args );

        if( $query->have_posts() ) {
            ?>
            <h3>Books</h3>
            <?php
            echo $args['before_widget'];
            if ( $title ) {
                echo $args['before_title'] . $title . $args['after_title'];
            }               

            while( $query->have_posts() ) {
                $query->the_post(); ?>
                
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>> 

                    <header class="entry-header">
                        <?php the_title( '<h3 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h3>' ); ?>
                    </header><!-- .entry-header -->

                    <?php 
 
                    if( true === $excerpt ) { ?>    

                        <div class="entry-summary">
                            <?php the_excerpt(); ?>
                        </div><!-- .entry-summary -->

                    <?php } ?>

                </article><!-- #post-## -->

                <?php
            }


            wp_reset_postdata();
        }
          //  echo $args['after_widget']; 

        if ( ! $this->is_preview() ) {
            $cache[ $args['widget_id'] ] = ob_get_flush();
            wp_cache_set( 'widget_cat_posts', $cache, 'widget' );
        } else {
            ob_end_flush();
        }
    }

    public function update( $new_instance, $old_instance ) 
    {
        
        $instance                   = $old_instance;
        $instance['title']          = strip_tags( $new_instance['title'] );
        $instance['number']         = (int) $new_instance['number'];
        $instance['cat_id']         = (int) $new_instance['cat_id'];
        $instance['rand']           = $new_instance['rand'];
        $instance['excerpt']        = $new_instance['excerpt'];
        $this->flush_widget_cache();

        $alloptions = wp_cache_get( 'alloptions', 'options' );
        if ( isset($alloptions['widget_category_posts']) )
            delete_option('widget_category_posts');

        return $instance;
    }

    public function flush_widget_cache() 
    {
        wp_cache_delete('widget_cat_posts', 'widget');
    }

    public function form( $instance ) 
    {
    
        $title      = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
        $number     = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
        $cat_id     = isset( $instance['cat_id'] ) ? esc_attr( $instance['cat_id'] ) : 1;
        $random     = isset( $instance['rand'] ) ? $instance['rand'] : false; 
        $excerpt    = isset( $instance['excerpt'] ) ? $instance['excerpt'] : false; 
        ?>

        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of posts to show:' ); ?></label>
            <input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $number; ?>" size="3" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('cat_id'); ?>"><?php _e( 'Category Name:' )?></label>
                 
            <?php 
                
                $args = array(
                    'type'                     => 'book',
                    'taxonomy'                 => 'genre', 
                );
                $categories = get_categories($args);   
                   
               ?>       
                <select id="<?php echo $this->get_field_id('cat_id'); ?>" name="<?php echo $this->get_field_name('cat_id'); ?>">
 
               
                <?php
                    
                foreach ( $categories as $cat ) {
                    $selected = ( $cat->term_id == esc_attr( $cat_id ) ) ? ' selected = "selected" ' : '';
                    $option = '<option '.$selected .'value="' . $cat->term_id;
                    $option = $option .'">';
                    $option = $option .$cat->name;
                    $option = $option .'</option>';
                    echo $option;
                }
               
                ?>
                
            </select>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('rand'); ?>"><?php _e( 'Show random posts' ); ?></label>
            <?php $checked = ( $random ) ? ' checked=\"checked\" ' : ''; ?>
            <input type="checkbox" id="<?php echo $this->get_field_id( 'rand' ); ?>" name="<?php echo $this->get_field_name( 'rand' ); ?>" value="true" <?php echo $checked; ?> />    
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('excerpt'); ?>"><?php _e( 'Show excerpt. If unchecked, only the title of the post will be displayed' ); ?></label>
            <?php $checked = ( $excerpt ) ? ' checked=\"checked\" ' : ''; ?>
            <input type="checkbox" id="<?php echo $this->get_field_id( 'excerpt' ); ?>" name="<?php echo $this->get_field_name( 'excerpt' ); ?>" value="true" <?php echo $checked; ?> />    
        </p>

    <?php
    }

}

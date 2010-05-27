<?php

/*
Plugin Name: Wincache Stats Widget
Plugin URI: http://wordpress.org/extend/plugins/wincache-stats/
Description: Widget to display wincache statistics.
Version: 1.0
Author: Kanwaljeet Singla
Author URI: http://www.ksingla.net/
*/

class Wincache_Stats_Widget extends WP_Widget
{
    function Wincache_Stats_Widget()
    {
        parent::WP_Widget( false, $name = 'Wincache Stats' );
    }

    function getpercent($hits, $misses)
    {
        $hit_percent = 0;
        $miss_percent = 0;

        if ( $hits < 0 ) $hits = 0;
        if ( $misses < 0 ) $misses = 0;

        if ( $hits > 0 || $misses > 0 )
        {
            $hit_percent = round( $hits / ( $hits + $misses ) * 100, 2 );
            $miss_percent = round( $misses / ( $hits + $misses ) * 100, 2 );
        }

        $data = array( 'Hits' => $hit_percent, 'Misses' => $miss_percent);
        return $data;
    }

    function widget( $args, $instance )
    {
        extract( $args );

        $title = apply_filters( 'widget_title', $instance['title'] );
        $show_ocache_stats = (bool)$instance['show_ocache_stats'];
        $show_fcache_stats = (bool)$instance['show_fcache_stats'];
        $show_ucache_stats = (bool)$instance['show_ucache_stats'];
        $show_scache_stats = (bool)$instance['show_scache_stats'];

        $output = '';
        $output .= '<table align="center"><tr><th>Cache</th><th>Hits</th><th>Misses</th></tr>';

        if($show_ocache_stats && function_exists( 'wincache_ocache_fileinfo' ))
        {
            $ocache_info = wincache_ocache_fileinfo( true );

            $filecount = $ocache_info[ 'total_file_count' ];
            $hits = $ocache_info[ 'total_hit_count' ];
            $misses = $ocache_info[ 'total_miss_count' ];

            $percent = $this->getpercent($hits, $misses);
            $output .= '<tr><td>Opcode<br/>(' . $filecount . ')</td><td>' . $hits . '<br/>(' . $percent['Hits'] . '%)</td><td>' . $misses . '<br/>(' . $percent['Misses'] . '%)</td></tr>';
        }

        if($show_fcache_stats && function_exists( 'wincache_fcache_fileinfo' ))
        {
            $fcache_info = wincache_fcache_fileinfo( true );

            $filecount = $fcache_info[ 'total_file_count' ];
            $hits = $fcache_info[ 'total_hit_count' ];
            $misses = $fcache_info[ 'total_miss_count' ];

            $percent = $this->getpercent($hits, $misses);
            $output .= '<tr><td>File<br/>(' . $filecount . ')</td><td>' . $hits . '<br/>(' . $percent['Hits'] . '%)</td><td>' . $misses . '<br/>(' . $percent['Misses'] . '%)</td></tr>';
        }

        if($show_ucache_stats && function_exists( 'wincache_ucache_info' ))
        {
            $ucache_info = wincache_ucache_info( true );

            $itemcount = $ucache_info[ 'total_item_count' ];
            $hits = $ucache_info[ 'total_hit_count' ];
            $misses = $ucache_info[ 'total_miss_count' ];

            $percent = $this->getpercent($hits, $misses);
            $output .= '<tr><td>Object<br/>(' . $itemcount . ')</td><td>' . $hits . '<br/>(' . $percent['Hits'] . '%)</td><td>' . $misses . '<br/>(' . $percent['Misses'] . '%)</td></tr>';
        }

        if($show_scache_stats && function_exists( 'wincache_scache_info' ))
        {
            $scache_info = wincache_scache_info( true );

            $itemcount = $scache_info[ 'total_item_count' ];
            $hits = $scache_info[ 'total_hit_count' ];
            $misses = $scache_info[ 'total_miss_count' ];

            $percent = $this->getpercent($hits, $misses);
            $output .= '<tr><td>Session<br/>(' . $itemcount . ')</td><td>' . $hits . '<br/>(' . $percent['Hits'] . '%)</td><td>' . $misses . '<br/>(' . $percent['Misses'] . '%)</td></tr>';
        }

        $output .= '</table>';

        if( $title )
        {
            $title = $before_title . $title . $after_title;
        }

        $output = $before_widget . $title . '<ul>' . $output . '</ul>' . $after_widget;
        echo $output;
    }

    function update( $new_instance, $old_instance )
    {
        $new_instance['show_ocache_stats'] = isset( $new_instance['show_ocache_stats'] );
        $new_instance['show_fcache_stats'] = isset( $new_instance['show_fcache_stats'] );
        $new_instance['show_ucache_stats'] = isset( $new_instance['show_ucache_stats'] );
        $new_instance['show_scache_stats'] = isset( $new_instance['show_scache_stats'] );

        return $new_instance;
    }

    function form( $instance )
    {
        $title = esc_attr( $instance['title'] );
        $show_ocache_stats = (bool)$instance[ 'show_ocache_stats' ];
        $show_fcache_stats = (bool)$instance[ 'show_fcache_stats' ];
        $show_ucache_stats = (bool)$instance[ 'show_ucache_stats' ];
        $show_scache_stats = (bool)$instance[ 'show_scache_stats' ];

        ?>
        <p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
        <p><label for="<?php echo $this->get_field_id( 'show_ocache_stats' ); ?>"><input id="<?php echo $this->get_field_id( 'show_ocache_stats' ); ?>" class="checkbox" type="checkbox" name="<?php echo $this->get_field_name( 'show_ocache_stats' ); ?>"<?php echo checked( $show_ocache_stats ); ?> /> <?php _e( 'Show Opcode Cache Statistics' ); ?></label></p>
        <p><label for="<?php echo $this->get_field_id( 'show_fcache_stats' ); ?>"><input id="<?php echo $this->get_field_id( 'show_fcache_stats' ); ?>" class="checkbox" type="checkbox" name="<?php echo $this->get_field_name( 'show_fcache_stats' ); ?>"<?php echo checked( $show_fcache_stats ); ?> /> <?php _e( 'Show File Cache Statistics' ); ?></label></p>
        <p><label for="<?php echo $this->get_field_id( 'show_ucache_stats' ); ?>"><input id="<?php echo $this->get_field_id( 'show_ucache_stats' ); ?>" class="checkbox" type="checkbox" name="<?php echo $this->get_field_name( 'show_ucache_stats' ); ?>"<?php echo checked( $show_ucache_stats ); ?> /> <?php _e( 'Show Object Cache Statistics' ); ?></label></p>
        <p><label for="<?php echo $this->get_field_id( 'show_scache_stats' ); ?>"><input id="<?php echo $this->get_field_id( 'show_scache_stats' ); ?>" class="checkbox" type="checkbox" name="<?php echo $this->get_field_name( 'show_scache_stats' ); ?>"<?php echo checked( $show_scache_stats ); ?> /> <?php _e( 'Show Session Cache Statistics' ); ?></label></p>
    <?php
    }
}

add_action( 'widgets_init', create_function( '', 'return register_widget( "Wincache_Stats_Widget" );' ) );

if ( !function_exists( 'mdv_most_commented' ) )
{
    function mdv_most_commented( $show_ocache_stats = true, $show_fcache_stats = true, $show_ucache_stats = true, $show_scache_stats = true)
    {
        $options = array(
                'show_ocache_stats' => $show_ocache_stats,
                'show_fcache_stats' => $show_fcache_stats,
                'show_ucache_stats' => $show_ucache_stats,
                'show_scache_stats' => $show_scache_stats
                );

        $args = array( 'widget_id' => 'wincache_stats_widget_' . md5( var_export( $options, true ) ) );

        $wincache_stats = new Wincache_Stats_Widget();
        $wincache_stats->widget( $args, $options );
    }
}

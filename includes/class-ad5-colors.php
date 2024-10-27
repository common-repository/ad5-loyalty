<?php
/**
 * Manage colors
 * 
 * @category Module
 * @package AD5
 * @author AD5
 */
class AD5_Colors
{
    const BLACK = "#000000";
    const WHITE = "#FFFFFF";

    private $r = 255;
    private $g = 255;
    private $b = 255;
    private $history = array();

    public function set_rgb( $r, $g, $b )
    {
        $this->push_history();
        $this->r = $r;
        $this->g = $g;
        $this->b = $b;
    }

    public function set_hex( $hex )
    {
        $this->push_history();
        $hex = str_replace('#', '', $hex);
        $this->r = hexdec(substr($hex, 0, 2));
        $this->g = hexdec(substr($hex, 2, 2));
        $this->b = hexdec(substr($hex, 4, 2));
    }

    public function get_hex()
    {
        return $this->rgb2hex( $this->r, $this->g, $this->b );
    }

    public function get_complementary( $dark = 0.5 )
    {
        $max = max( $this->r, $this->g, $this->b );
        $min = min( $this->r, $this->g, $this->b );
        $r = $max + $min - $this->r;
        $g = $max + $min - $this->g;
        $b = $max + $min - $this->b;        
        return $this->rgb2hex( $r, $g, $b );
    }

    public function get_darken( $dark = 0.5 )
    {
        $r = intval( $this->r * ( 1 - $dark ) );
        $g = intval( $this->g * ( 1 - $dark ) );
        $b = intval( $this->b * ( 1 - $dark ) );
        return $this->rgb2hex( $r, $g, $b );
    }

    public function get_brighten( $bright = 0.5 )
    {
        $r = $this->r + intval( ( 255 - $this->r ) * $bright );
        $g = $this->g + intval( ( 255 - $this->g ) * $bright );
        $b = $this->b + intval( ( 255 - $this->b ) * $bright );        
        return $this->rgb2hex( $r, $g, $b );
    }

    public function get_toned( $level = 0.5 )
    {
        if ( $this->is_dark() ) {
            return $this->get_darken( $level );
        } else {
            return $this->get_brighten( $level );
        }
    }

    public function get_counter_toned( $level = 0.5 )
    {
        if ( $this->is_dark() ) {
            return $this->get_brighten( $level );
        } else {
            return $this->get_darken( $level );
        }
    }

    public function get_threshold()
    {
        if ( $this->is_dark() ) {
            return self::BLACK;
        } else {
            return self::WHITE;
        }
    }

    public function get_counter_threshold()
    {
        if ( $this->is_dark() ) {
            return self::WHITE;
        } else {
            return self::BLACK;
        }
    }

    public function is_dark()
    {
        $brightness = $this->r + $this->g + $this->b;
        return ( $brightness < 378 );
    }

    public function history()
    {
        $history = array_shift( $this->history );
        list( $this->r, $this->g, $this->b ) = $history;
    }

    private function push_history()
    {
        $history = array( $this->r, $this->g, $this->b );
        array_unshift( $this->history, $history );
    }

    private function rgb2hex( $r, $g, $b ) {
        return sprintf('#%02X%02X%02X' , $r, $g, $b);
    }

    public static function grayscale ( $level = 0.5 ) {
        $w = intval( 255 * ( 1 - $level ) );
        return sprintf('#%02X%02X%02X' , $w, $w, $w);
    }
}


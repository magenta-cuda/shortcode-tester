<?php

namespace mc_debug_utilities {

    # This print_r() is necessary since the real print_r() uses output buffering and this causes the following error:
    #
    #     PHP Fatal error:  print_r(): Cannot use output buffering in output buffering display handlers ...
    #
    # when the real print_r() is used in output buffering display handlers.
    # N.B. this print_r() does not have the second boolean argument as it uses error_log().

    function print_r( $var, $name = '' ) {
        static $depth        = 0;
        static $done_objects = [];
        static $tabs         = '';
        ++$depth;
        $tabs  .= '    ';
        $delim  = '[';
        while ( TRUE ) {
            if ( is_object( $var ) ) {
                $object_hash = spl_object_hash( $var );
                if ( in_array( $object_hash, $done_objects ) ) {
                    error_log( "{$tabs}{$name} = *** RECURSION ***" );
                    break;
                }
                $done_objects[] = $object_hash;
                $class_name = get_class( $var );
                $var        = (array) $var;
                $delim      = '{';
            }
            if ( is_array( $var ) ) {
                error_log( "{$tabs}{$name} = " . ( $delim === '[' ? 'Array' : $class_name ) . '[' . sizeof( $var ) . "] = {$delim}" );
                foreach ( $var as $index => $value ) {
                    if ( ! ctype_print( $index ) ) {
                        # Fix protected and private property names of objects
                        $index = str_replace( "\x0", '-', $index );
                    }
                    \mc_debug_utilities\print_r( $value, "[$index]" );
                }
                error_log( "{$tabs}" . ( $delim === '{' ? '}' : ']' ) );
            } else {
                error_log( "{$tabs}{$name} = "
                    . ( is_string( $var ) ? '(String[' . strlen($var) . "]) = \"{$var}\""
                                                . ( strpos( $var, "\n" ) !== FALSE ? ' = (String[' . strlen($var) . "]) = {$name}" : '' )
                                          : ( $var === NULL ? 'NULL'
                                                            : ( is_bool ( $var ) ? ( $var ? 'TRUE' : 'FALSE' )
                                                                                 : "(Scalar) = {$var}" ) ) ) );
            }
            break;
        }   # while ( TRUE ) {
        $tabs = substr( $tabs, 0, -4 );
        if ( --$depth === 0 ) {
            $done_objects = [];
        }
    }
}   # namespace mc_debug_utilities {

?>

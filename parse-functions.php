<?php

namespace mc_html_parser {
    function get_start_tag( $buffer, $offset, $length ) {
        for ( ; $offset < $length; $offset++ ) {
            if ( $buffer[ $offset ] === '<' ) {
                if ( $buffer[ $offset + 1 ] !== '/' && $buffer[ $offset + 1 ] !== '!' ) {
                    return $offset;
                }
            }
        }
        return FALSE;
    }
    function get_name( $buffer, $offset, $length ) {
        for ( ; $offset < $length; $offset++ ) {
            if ( ! ctype_alpha( $buffer[ $offset ] ) ) {
                return $offset - 1;
            }
        }
        return $offset;
    }
    function get_greater_than( $buffer, $offset, $length ) {
        for ( ; $offset < $length; $offset++ ) {
            if ( $buffer[ $offset ] === '"' || $buffer[ $offset ] === '\'' ) {
                $offset = strpos( $buffer, $buffer[ $offset ], $offset + 1 );
                if ( $offset === FALSE ) {
                    return FALSE;
                }
                continue;
            }
            if ( $buffer[ $offset ] === '>' ) {
                return $offset;
            }
        }
        return FALSE;
    };
    function get_end_tag( $tag, $buffer, $offset, $length ) {
        for ( ; $offset < $length; $offset++ ) {
            if ( $buffer[ $offset ] === '<' ) {
                if ( $buffer[ $offset + 1 ] === '/' ) {
                    if ( substr_compare( $buffer, $tag, $offset + 2, strlen( $tag ) ) === 0 ) {
                        return $offset + 2 + strlen( $tag );
                    } else {
                        continue;
                    }
                } else if ( $buffer[ $offset + 1 ] !== '!' ) {
                    # This is an inner HTML element.
                    $prev_offset = $offset;
                    $offset      = get_name( $buffer, $offset + 1, $length );
                    $inner_tag   = substr( $buffer, $prev_offset + 1, $offset - $prev_offset );
                    if ( $inner_tag !== $tag ) {
                        continue;
                    }
                    $offset = get_greater_than( $buffer, $offset + 1, $length );
                    $offset = get_end_tag( $inner_tag, $buffer, $offset + 1, $length );
                    continue;
                }
            }
        }
        return FALSE;
    }
}   # namespace mc_html_parser {
?>

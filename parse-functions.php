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
            # Attributes may have string values.
            # Strings are dangerous as they may contain HTML tags so find the ending delimiter.
            if ( $buffer[ $offset ] === '"' || $buffer[ $offset ] === '\'' ) {
                $delim = $buffer[ $offset ];
                if ( ( $offset = strpos( $buffer, $delim, $offset + 1 ) ) === FALSE ) {
                    error_log( 'ERROR:\mc_html_parser\get_greater_than():Cannot find matching ending ' . $delim );
                    error_log( 'ERROR:\mc_html_parser\get_greater_than():The string attribute begins with: "' . substr( $buffer, $offset, 64 ) . '..."' );
                    return FALSE;
                }
                continue;
            }
            if ( $buffer[ $offset ] === '>' ) {
                return $offset;
            }
        }
        error_log( 'ERROR:\mc_html_parser\get_greater_than():Cannot find \'>\'' );
        error_log( 'ERROR:\mc_html_parser\get_greater_than():The buffer begins with: "' . substr( $buffer, $offset, 64 ) . '..."' );
        return FALSE;
    };
    function get_end_tag( $tag, $buffer, $offset, $length ) {
        if ( $tag === 'script' ) {
            # <script> elements are dangerous as they can contain strings which can contain HTML tags.
            # However, they are easier to parse as they cannot contain a nested <script> element.
            if ( ( $offset = strpos( $buffer, '</script>', $offset ) ) !== FALSE ) {
                return $offset + 8 - 1;
            }
            return FALSE;
        }
        for ( ; $offset < $length; $offset++ ) {
            # error_log( '\mc_html_parser\get_end_tag():substr( $buffer, $offset, 16 ) = ' . substr( $buffer, $offset, 16 ) );
            # Strings are dangerous as they may contain HTML tags so find the ending delimiter.
            if ( $buffer[ $offset ] === '"' || $buffer[ $offset ] === '\'' ) {
                $delim = $buffer[ $offset ];
                if ( ( $offset = strpos( $buffer, $delim, $offset + 1 ) ) === FALSE ) {
                    error_log( 'ERROR:\mc_html_parser\get_greater_than():Cannot find matching ending ' . $delim );
                    error_log( 'ERROR:\mc_html_parser\get_greater_than():The string attribute begins with: "' . substr( $buffer, $offset, 64 ) . '..."' );
                    return FALSE;
                }
                continue;
            }
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
                    # This is nested HTML element of the same tag.
                    # The ending tag </name> of this nested element is not the matching end tag and should be ignored.
                    if ( ( $gt_offset = get_greater_than( $buffer, $offset + 1, $length ) ) === FALSE ) {
                        error_log( 'ERROR:\mc_html_parser\get_end_tag():Cannot find matching \'>\'' );
                        error_log( 'ERROR:\mc_html_parser\get_end_tag():The HTML element begins with: "' . substr( $buffer, $prev_offset, 64 ) . '..."' );
                        return FALSE;
                    }
                    if ( ( $offset = get_end_tag( $inner_tag, $buffer, $gt_offset + 1, $length ) ) === FALSE ) {
                        # error_log( 'ERROR:\mc_html_parser\get_end_tag():Cannot find matching end tag "</' . $inner_tag . '>".' );
                        # error_log( 'ERROR:\mc_html_parser\get_end_tag():The HTML element begins with: "' . substr( $buffer, $prev_offset, 64 ) . '..."' );
                        # If we are parsing a HTML fragment then this may not be an error as the fragment may not yet be complete.
                        # The caller should handle this possible error.
                        return FALSE;
                    }
                    continue;
                }
            }
        }
        # error_log( 'ERROR:\mc_html_parser\get_end_tag():Cannot find matching end tag "</' . $tag . '>".' );
        # error_log( 'ERROR:\mc_html_parser\get_end_tag():substr( $buffer, $offset - 16, 16 ) = ' . substr( $buffer, $offset - 16, 16 ) );
        # error_log( 'ERROR:\mc_html_parser\get_end_tag():substr( $buffer, $length - 16, 16 ) = ' . substr( $buffer, $length - 16, 16 ) );
        # If we are parsing a HTML fragment then this may not be an error as the fragment may not yet be complete.
        # The caller should handle this possible error.
        return FALSE;
    }
}   # namespace mc_html_parser {
?>

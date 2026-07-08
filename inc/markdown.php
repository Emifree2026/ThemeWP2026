<?php
/**
 * Minimal Markdown -> HTML converter used for legal pages.
 * Supports headings (#, ##, ###), unordered lists (-/*), paragraphs,
 * bold **text**, italic *text*, and links [text](url).
 */
function emifree_simple_markdown_to_html( $md ) {
    // Unescape common escaped markdown markers produced by the source
    $md = str_replace('\\#', '#', $md);
    $md = str_replace('\\*', '*', $md);
    $md = str_replace('\\-', '-', $md);
    $lines = preg_split("/\r\n|\n|\r/", $md);
    $out = '';
    $in_list = false;
    $buffer = array();

    $flush_paragraph = function() use ( & $buffer, & $out ) {
        if ( ! empty( $buffer ) ) {
            $p = implode("\n", $buffer);
            $p = trim( $p );
            if ( $p !== '' ) {
                $out .= '<p class="text-lg leading-relaxed mb-6">' . emifree_markdown_span( $p ) . '</p>' . "\n";
            }
            $buffer = array();
        }
    };

    foreach ( $lines as $line ) {
        $trim = trim( $line );
        if ( $trim === '' ) {
            if ( $in_list ) {
                $out .= "</ul>\n";
                $in_list = false;
            }
            $flush_paragraph();
            continue;
        }
        // Headings
        if ( preg_match( '/^###\s+(.*)$/', $trim, $m ) ) {
            $flush_paragraph();
            $out .= '<h3 class="text-xl font-semibold text-zinc-900 mt-8 mb-3">' . emifree_markdown_span( $m[1] ) . '</h3>' . "\n";
            continue;
        }
        if ( preg_match( '/^##\s+(.*)$/', $trim, $m ) ) {
            $flush_paragraph();
            $out .= '<h2 class="text-2xl font-bold text-zinc-900 mt-12 mb-4">' . emifree_markdown_span( $m[1] ) . '</h2>' . "\n";
            continue;
        }
        if ( preg_match( '/^#\s+(.*)$/', $trim, $m ) ) {
            $flush_paragraph();
            $out .= '<h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-zinc-900 leading-tight mb-6">' . emifree_markdown_span( $m[1] ) . '</h1>' . "\n";
            continue;
        }

        // Unordered list
        if ( preg_match( '/^[-\*]\s+(.*)$/', $trim, $m ) ) {
            $flush_paragraph();
            if ( ! $in_list ) {
                $out .= "<ul class=\"text-lg leading-relaxed mb-6 space-y-3 list-disc pl-6\">\n";
                $in_list = true;
            }
            $out .= "<li>" . emifree_markdown_span( $m[1] ) . "</li>\n";
            continue;
        }

        // Normal paragraph line — collect into buffer
        $buffer[] = $trim;
    }

    if ( $in_list ) {
        $out .= "</ul>\n";
    }
    // flush last paragraph
    $flush_paragraph();
    return $out;
}

function emifree_markdown_span( $text ) {
    // links [text](url)
    $text = preg_replace_callback( '/\[([^\]]+)\]\(([^)]+)\)/', function( $m ) {
        $label = esc_html( $m[1] );
        $url = esc_url( $m[2] );
        return '<a href="' . $url . '" class="text-blue-700 hover:text-blue-800">' . $label . '</a>';
    }, $text );
    // bold **text**
    $text = preg_replace( '/\*\*(.*?)\*\*/', '<strong>$1</strong>', $text );
    // italic *text*
    $text = preg_replace( '/\*(.*?)\*/', '<em>$1</em>', $text );
    return $text;
}

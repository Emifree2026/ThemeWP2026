<?php
// Standalone markdown rendering test (no theme includes). Usage:
// php tests/md-run.php path/to/file.md
$md_file = $argv[1] ?? __DIR__ . '/../assets/Legal/impressum_en.md';
if ( ! file_exists( $md_file ) ) {
    fwrite( STDERR, "Markdown file not found: $md_file\n" );
    exit(1);
}
$md_text = file_get_contents( $md_file );
if ( $md_text === false ) {
    fwrite( STDERR, "Failed to read markdown file: $md_file\n" );
    exit(1);
}

function emifree_markdown_span_local( $text ) {
    $text = preg_replace_callback( '/\[([^\]]+)\]\(([^)]+)\)/', function( $m ) {
        $label = htmlspecialchars( $m[1], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );
        $url = filter_var( $m[2], FILTER_SANITIZE_URL );
        return '<a href="' . $url . '" class="text-blue-700 hover:text-blue-800">' . $label . '</a>';
    }, $text );
    $text = preg_replace( '/\*\*(.*?)\*\*/', '<strong>$1</strong>', $text );
    $text = preg_replace( '/\*(.*?)\*/', '<em>$1</em>', $text );
    return $text;
}

function emifree_simple_markdown_to_html_local( $md ) {
    // Unescape common escaped markdown markers found in source files
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
                $out .= '<p class="text-lg leading-relaxed mb-6">' . emifree_markdown_span_local( $p ) . '</p>' . "\n";
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
        if ( preg_match( '/^###\s+(.*)$/', $trim, $m ) ) {
            $flush_paragraph();
            $out .= '<h3 class="text-xl font-semibold text-zinc-900 mt-8 mb-3">' . emifree_markdown_span_local( $m[1] ) . '</h3>' . "\n";
            continue;
        }
        if ( preg_match( '/^##\s+(.*)$/', $trim, $m ) ) {
            $flush_paragraph();
            $out .= '<h2 class="text-2xl font-bold text-zinc-900 mt-12 mb-4">' . emifree_markdown_span_local( $m[1] ) . '</h2>' . "\n";
            continue;
        }
        if ( preg_match( '/^#\s+(.*)$/', $trim, $m ) ) {
            $flush_paragraph();
            $out .= '<h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-zinc-900 leading-tight mb-6">' . emifree_markdown_span_local( $m[1] ) . '</h1>' . "\n";
            continue;
        }
        if ( preg_match( '/^[-\*]\s+(.*)$/', $trim, $m ) ) {
            $flush_paragraph();
            if ( ! $in_list ) {
                $out .= '<ul class="text-lg leading-relaxed mb-6 space-y-3 list-disc pl-6">\n';
                $in_list = true;
            }
            $out .= '<li>' . emifree_markdown_span_local( $m[1] ) . '</li>\n';
            continue;
        }
        $buffer[] = $trim;
    }
    if ( $in_list ) {
        $out .= "</ul>\n";
    }
    $flush_paragraph();
    return $out;
}

$html = emifree_simple_markdown_to_html_local( $md_text );
echo "----- HTML OUTPUT -----\n";
echo $html . PHP_EOL;
echo "----- END -----\n";

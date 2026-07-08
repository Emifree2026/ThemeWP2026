<?php
// Simple test runner for emifree_simple_markdown_to_html
// Usage: php tests/legal-md-test.php [path-to-md]
$md = $argv[1] ?? __DIR__ . '/../assets/Legal/impressum_en.md';
if ( ! file_exists( $md ) ) {
    fwrite( STDERR, "Markdown file not found: $md\n" );
    exit(1);
}

// Provide minimal WP escape functions if not present so the parser can run standalone
if ( ! function_exists( 'esc_html' ) ) {
    function esc_html( $str ) {
        return htmlspecialchars( $str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );
    }
}
if ( ! function_exists( 'esc_url' ) ) {
    function esc_url( $url ) {
        return filter_var( $url, FILTER_SANITIZE_URL );
    }
}

// Load the legal helper which defines emifree_simple_markdown_to_html
require_once __DIR__ . '/../inc/legal.php';
$md_text = file_get_contents( $md );
if ( $md_text === false ) {
    fwrite( STDERR, "Failed to read markdown file: $md\n" );
    exit(1);
}

$html = emifree_simple_markdown_to_html( $md_text );
echo "----- HTML OUTPUT -----\n";
echo $html . PHP_EOL;
echo "----- END -----\n";

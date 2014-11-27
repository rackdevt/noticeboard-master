<?php

global $wpdb;

$wpdb->query( $wpdb->prepare( 'DELETE FROM ' . $wpdb->postmeta . ' WHERE meta_key="hide-page-title- Titles_toggle_title"' ) );
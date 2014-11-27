jQuery( function( $ ) {

  $( 'body' ).on( 'change', '.menu-item-if-menu-enable', function() {
    $( this ).closest( '.if-menu-enable' ).next().toggle( $( this ).prop( 'checked' ) );
  } );

} );
/**
 * Order on WhatsApp for WooCommerce
 *
 * On product pages, fills {quantity}, {variation} and {price} in the message
 * with what the customer picked, right before WhatsApp opens.
 *
 * @package OOW
 */
( function ( $ ) {
	'use strict';

	function buttonFor( el ) {
		var $b = $( el ).closest( '.product' ).find( '.oow-product-button' ).first();
		return $b.length ? $b : $( '.oow-product-button' ).first();
	}

	function formFor( $btn ) {
		var $f = $btn.closest( '.product' ).find( 'form.cart' ).first();
		return $f.length ? $f : $( 'form.cart' ).first();
	}

	function variationText( $form ) {
		var parts = [];

		$form.find( '.variations select' ).each( function () {
			var $select = $( this );

			if ( ! $select.val() ) {
				return;
			}

			var label = $.trim( $select.closest( 'tr' ).find( 'th label, td.label label' ).first().text() );

			if ( ! label ) {
				label = this.name.replace( /^attribute_(pa_)?/, '' );
			}

			parts.push( label + ': ' + $.trim( $select.find( 'option:selected' ).text() ) );
		} );

		return parts.join( ', ' );
	}

	function fill( $btn ) {
		var tpl = $btn.attr( 'data-template' );

		if ( ! tpl ) {
			return;
		}

		var $form = formFor( $btn );
		var qty   = parseInt( $form.find( 'input.qty' ).val(), 10 ) || 1;
		var price = $btn.data( 'vprice' ) || $btn.attr( 'data-price' ) || '';
		var msg   = tpl
			.split( '{quantity}' ).join( qty )
			.split( '{variation}' ).join( variationText( $form ) )
			.split( '{price}' ).join( price );

		msg = $.trim( msg.replace( /^[ \t]+$/gm, '' ).replace( /\n{3,}/g, '\n\n' ) );

		$btn.attr( 'href', 'https://wa.me/' + $btn.attr( 'data-phone' ) + '?text=' + encodeURIComponent( msg ) );
	}

	function setEnabled( $btn, enabled ) {
		$btn.toggleClass( 'oow-disabled', ! enabled ).attr( 'aria-disabled', enabled ? 'false' : 'true' );

		if ( enabled ) {
			$btn.siblings( '.oow-note' ).prop( 'hidden', true ).text( '' );
		}
	}

	$( document ).on( 'found_variation', 'form.variations_form', function ( event, variation ) {
		var $btn = buttonFor( this );
		var text = $.trim( $( '<div>' ).html( variation.price_html || '' ).text() );

		// When every variation has the same price WooCommerce sends no price_html.
		$btn.data( 'vprice', text );
		setEnabled( $btn, true );
		fill( $btn );
	} );

	$( document ).on( 'reset_data hide_variation', 'form.variations_form', function () {
		var $btn = buttonFor( this );

		$btn.data( 'vprice', '' );
		setEnabled( $btn, false );
	} );

	$( document ).on( 'change input', 'form.cart input.qty', function () {
		fill( buttonFor( this ) );
	} );

	$( document ).on( 'click', '.oow-product-button', function ( event ) {
		var $btn = $( this );

		if ( $btn.hasClass( 'oow-disabled' ) ) {
			event.preventDefault();
			$btn.siblings( '.oow-note' ).text( $btn.attr( 'data-choose' ) ).prop( 'hidden', false );
			return;
		}

		fill( $btn );
	} );

	$( function () {
		$( '.oow-product-button' ).each( function () {
			fill( $( this ) );
		} );
	} );
}( jQuery ) );

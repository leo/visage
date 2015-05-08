var $ = jQuery,
	file_frame,
	set_to_post_id = 10,
	not_admin;

$( window ).load( function() {

	var anchor = $( '#your-profile' ).find( 'h3' ).eq( 2 ).next( '.form-table' );
	not_admin = $( 'body' ).hasClass( 'user-edit-php' );

	$( '.visage' ).insertAfter( anchor );

});

$( document ).ready( function() {

	$( '<img/>' )[0].src = $( 'input[name="visage-default"]' ).val();

});

$( '.visage-upload' ).live( 'click', function( event ) {

	event.preventDefault();

	if( file_frame ) {

		file_frame.uploader.uploader.param( 'post_id', set_to_post_id );
		file_frame.open();
		return;

	}

	file_frame = wp.media.frames.file_frame = wp.media( {

		title: visage_lang.media_title,

		button: {
			text: visage_lang.media_button
		},

		library: {
			type: 'image'
		},

		editing: false,
		multiple: false

	});

	file_frame.on( 'select', function() {

		attachment = file_frame.state().get( 'selection' ).first().toJSON();
		$( 'input[name="visage-current"]' ).attr( 'value', attachment.id );

		if( not_admin ) {

			$( '.visage img.avatar' ).attr( 'src', attachment.sizes.thumbnail.url );

		} else {

			$( 'img.avatar' ).attr( 'src', attachment.sizes.thumbnail.url );

		}

		$( '.visage-delete, [name="visage-rating"]' ).removeClass( 'hidden' );

	});

	file_frame.on( 'open', function() {

		var selection = file_frame.state().get( 'selection' );
		var id = $( 'input[name="visage-current"]' ).val();
		attachment = wp.media.attachment(id);
		attachment.fetch();
		selection.add(attachment ? [attachment] : []);

	});

	file_frame.open();

});

$( '.visage-delete' ).live( 'click', function( event ) {

	event.preventDefault();

	$( 'input[name="visage-current"]' ).attr( 'value', 0 );

	if( not_admin ) {

		$( '.visage img.avatar' ).attr( 'src', $('input[name="visage-default"]').val() );

	} else {

		$( 'img.avatar' ).attr( 'src', $('input[name="visage-default"]' ).val() );

	}

	$( this ).addClass( 'hidden' );
	$( '[name="visage-rating"]' ).addClass( 'hidden' );

});
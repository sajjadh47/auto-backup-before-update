/* jshint esversion: 6 */
jQuery( document ).ready( function( $ ) {
	$( '.delete-backup' ).click( function( event ) {
		if ( confirm( AbbuAutoBackupBeforeUpdate.deleteConfirmMsg ) ) {
			handleBackupAction( this );
		}
	} );

	$( '.restore-backup' ).click( function( event ) {
		if ( confirm( AbbuAutoBackupBeforeUpdate.restoreConfirmMsg ) ) {
			if ( confirm( AbbuAutoBackupBeforeUpdate.restoreConfirmMsg2 ) ) {
				handleBackupAction( this );
			}
		}

		event.preventDefault();
	} );

	$( 'td.versions.column-versions' ).attr( 'colspan', '2' );

	function handleBackupAction( button ) {
		$( button ).data( 'original-text', $( button ).text() );

		var url     = $( button ).data( 'url' );
		var action  = $( button ).data( 'action' );
		var version = $( button ).data( 'version' );
		var slug    = $( button ).data( 'slug' );
		var type    = $( button ).data( 'type' );

		$( '.versions.column-versions .button' ).prop( 'disabled', true );
		
		$( button ).text( AbbuAutoBackupBeforeUpdate.processingTxt );

		// Remove old notices first.
		$( '.abbu-notice' ).remove();

		$.ajax( {
			url: ajaxurl,
			method: 'POST',
			data: {
				action: 'abbu_backup_manager',
				_wpnonce: AbbuAutoBackupBeforeUpdate._wpnonce,
				backup_action: action,
				version: version,
				slug: slug,
				type: type,
			},
			success: function( response ) {
				var message    = response.data && response.data.message ? response.data.message : 'Unknown response.';
				var noticeType = response.success ? 'success' : 'error'; // WP core notice classes.

				if ( response.success && action == 'delete' ) {
					$( button ).fadeOut( 200, function() { $( this ).closest( 'tr' ).remove(); } );
				}

				// Create new notice
				var $notice = $(`
					<div class="notice notice-${ noticeType } is-dismissible abbu-notice">
						<p>${ message }</p>
						<button type="button" class="notice-dismiss">
							<span class="screen-reader-text">${ AbbuAutoBackupBeforeUpdate.dismissNoticeTxt }</span>
						</button>
					</div>
				`);

				$( '.abbu_backups table.backups' ).before( $notice );

				// Handle dismiss click
				$notice.on( 'click', '.notice-dismiss', function() {
					$notice.fadeOut( 200, function() { $( this ).remove(); } );
				} );
			},
			error: function() {
				let $errorNotice = $(`
					<div class="notice notice-error is-dismissible abbu-notice">
						<p>${ AbbuAutoBackupBeforeUpdate.requestFailedTxt }</p>
						<button type="button" class="notice-dismiss">
							<span class="screen-reader-text">${ AbbuAutoBackupBeforeUpdate.dismissNoticeTxt }</span>
						</button>
					</div>
				`);

				$( '.abbu_backups table.backups' ).before( $errorNotice );
			},
			complete: function() {
				$( button).text( $( button ).data( 'original-text' ) );

				$( '.versions.column-versions .button' ).prop( 'disabled', false );
			}
		} );
	}
} );
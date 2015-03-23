<?php
/** Overlay actions
  *  - delete overlay
  *  - activate
  *  - deactivate
  */
	$otw_overlay_values = array(
		'title'       =>  '',
		'status'      =>  'inactive'
	);
	
	$otw_overlay_id = '';
	$otw_action = '';
	
	if( isset( $_GET['action'] ) ){
		
		switch( $_GET['action'] ){
			
			case 'delete':
					$otw_action = 'otw_pcl_overlay_delete';
					$page_title = __( 'Delete Overlay', 'otw_pcl' );
					$confirm_text = __( 'Please confirm to delete the overlay', 'otw_pcl' );
				break;
			case 'activate':
					$otw_action = 'otw_pcl_overlay_activate';
					$page_title = __( 'Activate Overlay', 'otw_pcl' );
					$confirm_text = __( 'Please confirm to activate the overlay', 'otw_pcl' );
				break;
			case 'deactivate':
					$otw_action = 'otw_pcl_overlay_deactivate';
					$page_title = __( 'Deactivate Overlay', 'otw_pcl' );
					$confirm_text = __( 'Please confirm to deactivate the overlay', 'otw_pcl' );
				break;
		}
	}
	if( !$otw_action ){
		wp_die( __( 'Invalid overlay action', 'otw_pcl' ) );
	}
	if( isset( $_GET['overlay'] ) ){
		
		$otw_overlay_id = $_GET['overlay'];
		$otw_overlays = otw_get_overlays();
		
		if( is_array( $otw_overlays ) && isset( $otw_overlays[ $otw_overlay_id ] ) ){
			
			$otw_overlay_values['title'] = $otw_overlays[ $otw_overlay_id ]['title'];
			$otw_overlay_values['status'] = $otw_overlays[ $otw_overlay_id ]['status'];
			$otw_overlay_values['validfor'] = $otw_overlays[ $otw_overlay_id ]['validfor'];
		}
	}
	if( !$otw_overlay_id ){
		wp_die( __( 'Invalid overlay', 'otw_pcl' ) );
	}
	
?>
<div class="wrap">
	<div id="icon-edit" class="icon32"><br/></div>
	<h2>
		<?php echo $page_title; ?>
		<a class="preview button" href="admin.php?page=otw-pcl"><?php _e( 'Back To List Of overlays', 'otw_pcl' );?></a>
	</h2>
	<?php include_once( 'otw_pcl_help.php' );?>
	<div id="ajax-response"></div>
	<div class="form-wrap" id="poststuff">
		<form method="post" action="" class="validate">
			<input type="hidden" name="otw_action" value="<?php echo $otw_action?>" />
			<?php wp_original_referer_field(true, 'previous'); wp_nonce_field('otw-pcl-overlays-action'); ?>

			<div id="post-body">
				<div id="post-body-content">
					<div id="col-right">
						<div class="form-field form-required">
							<?php _e( 'overlay title', 'otw_pcl' );?>:
							<strong><?php echo $otw_overlay_values['title']?></strong>
						</div>
						<div class="form-field">
							<?php _e( 'Status', 'otw_pcl' );?>:
							<strong><?php _e( ucfirst( $otw_overlay_values['status']  ), 'otw_pcl' )?></strong>
						</div>
					</div>
					<div id="col-left">
						<p>
							<?php echo $confirm_text;?>
						</p>
						<p class="submit">
							<input type="submit" class="button button-primary button-large" value="<?php _e( 'Confirm', 'otw_pcl') ?>" name="submit" />
							<input type="submit" value="<?php _e( 'Cancel', 'otw_pcl' ) ?>" name="cancel" class="button"/>
						</p>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
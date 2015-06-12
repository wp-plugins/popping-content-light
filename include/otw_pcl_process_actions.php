<?php
/**
 * Process otw actions
 *
 */
if( isset( $_POST['otw_action'] ) ){

	switch( $_POST['otw_action'] ){
		
		case 'otw_pcl_overlay_activate':
				if( isset( $_POST['cancel'] ) ){
					wp_redirect( 'admin.php?page=otw-pcl' );
				}else{
					$otw_overlays = otw_get_overlays();
					
					if( isset( $_GET['overlay'] ) && isset( $otw_overlays[ $_GET['overlay'] ] ) ){
						$otw_overlay_id = $_GET['overlay'];
						
						$otw_overlays[ $otw_overlay_id ]['status'] = 'active';
						
						otw_save_overlays( $otw_overlays );
						
						wp_redirect( 'admin.php?page=otw-pcl&message=3' );
					}else{
						wp_die( __( 'Invalid overlay', 'otw_pcl' ) );
					}
				}
			break;
		case 'otw_pcl_overlay_deactivate':
				if( isset( $_POST['cancel'] ) ){
					wp_redirect( 'admin.php?page=otw-pcl' );
				}else{
					$otw_overlays = otw_get_overlays();
					
					if( isset( $_GET['overlay'] ) && isset( $otw_overlays[ $_GET['overlay'] ] ) ){
						$otw_overlay_id = $_GET['overlay'];
						
						$otw_overlays[ $otw_overlay_id ]['status'] = 'inactive';
						
						otw_save_overlays( $otw_overlays );
						
						wp_redirect( 'admin.php?page=otw-pcl&message=4' );
					}else{
						wp_die( __( 'Invalid overlay', 'otw_pcl' ) );
					}
				}
			break;
		case 'otw_pcl_overlay_delete':
				if( isset( $_POST['cancel'] ) ){
					wp_redirect( 'admin.php?page=otw-pcl' );
				}else{
					
					$otw_overlays = otw_get_overlays();
					
					if( isset( $_GET['overlay'] ) && isset( $otw_overlays[ $_GET['overlay'] ] ) ){
						$otw_overlay_id = $_GET['overlay'];
						
						$new_overlays = array();
						
						//remove the overlay from otw_overlays
						foreach( $otw_overlays as $overlay_key => $overlay ){
						
							if( $overlay_key != $otw_overlay_id ){
							
								$new_overlays[ $overlay_key ] = $overlay;
							}
						}
						otw_save_overlays( $new_overlays );
						
						wp_redirect( admin_url( 'admin.php?page=otw-pcl&message=2' ) );
					}else{
						wp_die( __( 'Invalid overlay', 'otw_pcl' ) );
					}
				}
			break;
		case 'manage_otw_pcl_overlay':
				
				global $validate_messages, $wpdb, $otw_pcl_overlay_object;
				
				$validate_messages = array();
				
				$valid_page = true;
				if( !isset( $_POST['title'] ) || !strlen( trim( $_POST['title'] ) ) ){
					$valid_page = false;
					$validate_messages[] = __( 'Please type valid overlay title', 'otw_pcl' );
				}
				if( !isset( $_POST['status'] ) || !strlen( trim( $_POST['status'] ) ) ){
					$valid_page = false;
					$validate_messages[] = __( 'Please select status', 'otw_pcl' );
				}
				if( !isset( $_POST['type'] ) || !strlen( trim( $_POST['type'] ) ) ){
					$valid_page = false;
					$validate_messages[] = __( 'Please select overlay type', 'otw_pcl' );
				}
				if( $valid_page ){
					$otw_overlays = otw_get_overlays();
					
					if( isset( $_GET['overlay'] ) && isset( $otw_overlays[ $_GET['overlay'] ] ) ){
						$otw_overlay_id = $_GET['overlay'];
						$overlay = $otw_overlays[ $_GET['overlay'] ];
					}else{
						$overlay = array();
						$otw_overlay_id = false;
					}
					
					$overlay['title'] = (string) $_POST['title'];
					$overlay['type'] = (string) $_POST['type'];
					$overlay['status'] = (string) $_POST['status'];
					$overlay['grid_content'] = $_POST['_otw_grid_manager_content']['code'];
					$overlay['options'] = array();
					
					//save options
					foreach( $otw_pcl_overlay_object->overlay_types as $overlay_type => $overlay_type_data ){
						
						foreach( $overlay_type_data['options'] as $o_type => $type_options ){
							
							if( in_array( $o_type, array( 'main', 'custom' ) ) ){
								
								foreach( $type_options['items'] as $option_name => $option_item ){
									
									if( isset( $_POST[$overlay_type.'_'.$option_name] ) ){
										
										$overlay['options'][ $overlay_type.'_'.$option_name ] = $_POST[$overlay_type.'_'.$option_name];
										
									}elseif( isset( $overlay['options'][ $overlay_type.'_'.$option_name ] ) ){
										
										unset( $overlay['options'][ $overlay_type.'_'.$option_name ] );
									}
									
									if( isset( $option_item['subfields'] ) && is_array( $option_item['subfields'] ) && count( $option_item['subfields'] ) ){
									
										foreach( $option_item['subfields'] as $subfield => $subfield_data ){
										
											if( isset( $_POST[$overlay_type.'_'.$option_name.'_'.$subfield ] ) ){
												
												$overlay['options'][ $overlay_type.'_'.$option_name.'_'.$subfield ] = $_POST[$overlay_type.'_'.$option_name.'_'.$subfield];
												
											}elseif( isset( $overlay['options'][ $overlay_type.'_'.$option_name.'_'.$subfield  ] ) ){
												
												unset( $overlay['options'][ $overlay_type.'_'.$option_name.'_'.$subfield  ] );
											}
										}
									
									}
								}
								
							}else{
								foreach( $type_options['items'] as $option_name => $option_item ){
									
									$overlay['options'][ $overlay_type.'_'.$option_name ] = $option_item['default'];
								}
							}
						}
					}
					
					if( $otw_overlay_id === false ){
						
						$otw_overlay_id = 'otw-overlay-'.( otw_get_next_overlay_id() );
						$overlay['id'] = $otw_overlay_id;
					}
					
					$otw_overlays[ $otw_overlay_id ] = $overlay;
					
					if( !otw_save_overlays( $otw_overlays ) && $wpdb->last_error ){
						
						$valid_page = false;
						$validate_messages[] = __( 'DB Error: ', 'otw_pcl' ).$wpdb->last_error.'. Tring to save '.strlen( maybe_serialize( $otw_overlays ) ).' bytes.';
					}else{
						wp_redirect( 'admin.php?page=otw-pcl&message=1' );
					}
				}
			break;
	}
}

<?php
class OTW_Overlay_Shortcode_Ordered_List extends OTW_Overlay_Shortcodes{
	
	public function __construct(){
		
		$this->has_custom_options = true;
		
		parent::__construct();
		
		$this->shortcode_name = 'otw_shortcode_ordered_list';
	}
	/**
	 * register external libs
	 */
	public function register_external_libs(){
	
		$this->add_external_lib( 'css', 'otw-shortcode', $this->component_url.'css/otw_shortcode.css', 'all', 100 );
	}
	/**
	 * apply settings
	 */
	public function apply_settings(){
		
		$this->settings = array(
		
			'items' => array(
				'1'           => $this->get_label( '1 Item' ),
				'2'           => $this->get_label( '2 Items(default)' ),
				'3'           => $this->get_label( '3 Items' ),
				'4'           => $this->get_label( '4 Items' ),
				'5'           => $this->get_label( '5 Items' ),
				'6'           => $this->get_label( '6 Items' ),
				'7'           => $this->get_label( '7 Items' ),
				'8'           => $this->get_label( '8 Items' ),
				'9'           => $this->get_label( '9 Items' ),
				'10'          => $this->get_label( '10 Items' )
			),
			'default_items' => 2,
			
			'style' => array(
				'armenian'             => 'armenian',
				'decimal'              => 'decimal',
				'decimal-leading-zero' => 'decimal-leading-zero',
				'georgian'             => 'georgian',
				'lower-alpha'          => 'lower-alpha',
				'lower-greek'          => 'lower-greek',
				'lower-latin'          => 'lower-latin',
				'lower-roman'          => 'lower-roman',
				'upper-alpha'          => 'upper-alpha',
				'upper-latin'          => 'upper-latin',
				'upper-roman'          => 'upper-roman'
			
			),
			'default_style' => 'decimal',
			
			'default_item_text' => $this->get_label( 'Place your list items here' )
		);
		
	}
	
	/**
	 * Shortcode admin interface
	 */
	public function build_shortcode_editor_options(){
		
		$html = '';
		
		$source = array();
		if( isset( $_POST['shortcode_object'] ) ){
			$source = $_POST['shortcode_object'];
		}
		
		$html .= OTW_Form::select( array( 'id' => 'otw-shortcode-element-items', 'label' => $this->get_label( 'Items' ), 'description' => $this->get_label( 'Select number of items.' ), 'parse' => $source, 'options' => $this->settings['items'], 'value' => $this->settings['default_items'], 'data-reload' => '1' ) );
		
		$html .= OTW_Form::select( array( 'id' => 'otw-shortcode-element-style', 'label' => $this->get_label( 'List style' ), 'description' => $this->get_label( 'Select the style of the items.' ), 'parse' => $source, 'options' => $this->settings['style'], 'value' => $this->settings['default_style'] ) );
		
		$total_items = $this->settings['default_items'];
		
		if( isset( $source['otw-shortcode-element-items'] ) ){
			$total_items = $source['otw-shortcode-element-items'];
		}
		
		for( $cT = 1; $cT <= $total_items; $cT++ )
		{
			$html .= OTW_Form::text_input( array( 'id' => 'otw-shortcode-element-item_'.$cT.'_name', 'label' => $this->get_label( 'Item '.$cT.' title' ), 'description' => $this->get_label( 'The item content.' ), 'parse' => $source, 'value' => $this->settings['default_item_text'] )  );
		}
		
		return $html;
	}
	
	/**
	 * Shortcode admin interface custom options
	 */
	public function build_shortcode_editor_custom_options(){
		
		$html = '';
		
		$source = array();
		if( isset( $_POST['shortcode_object'] ) ){
			$source = $_POST['shortcode_object'];
		}
		
		$html .= OTW_Form::text_input( array( 'id' => 'otw-shortcode-element-css_class', 'label' => $this->get_label( 'CSS Class' ), 'description' => $this->get_label( 'If you\'d like to style this element separately enter a name here. A CSS class with this name will be available for you to style this particular element..' ), 'parse' => $source )  );
		
		return $html;
	}
	
	/** build shortcode
	 *
	 *  @param array
	 *  @return string
	 */
	public function build_shortcode_code( $attributes ){
		
		$code = '';
		
		if( !$this->has_error ){
		
			$code = '[otw_shortcode_ordered_list';
			
			$code .= $this->format_attribute( 'items', 'items', $attributes );
			
			$code .= $this->format_attribute( 'style', 'style', $attributes );
			
			if( $items = $this->format_attribute( '', 'items', $attributes ) ){
				
				for( $cT = 1; $cT <= $items; $cT++ ){
					$code .= $this->format_attribute( 'item_'.$cT.'_name', 'item_'.$cT.'_name', $attributes, false, '', true );
				}
			}
			
			$code .= $this->format_attribute( 'css_class', 'css_class', $attributes, false, '', true  );
			
			$code .= ']';
			
			$code .= '[/otw_shortcode_ordered_list]';
		}
		
		return $code;

	}
	
	/**
	 * Display shortcode
	 */
	public function display_shortcode( $attributes, $content ){
		
		$html = '<div';
		
		/*class attributes*/
		$class = 'otw-sc-list';
		
		$class .= $this->format_attribute( '', 'css_class', $attributes, false, $class );
		
		if( strlen( $class ) ){
			$html .= ' class="'.$class.'"';
		}
		/*end class attributes*/
		
		$html .= '>';
		$html .= '<ol';
		
		$ul_class = 'otw-list';
		
		if( strlen( $ul_class ) ){
			$html .= ' class="'.$ul_class.'"';
		}
		
		/*style attribute*/
		$style = '';
		
		if( $ol_style = $this->format_attribute( '', 'style', $attributes, false, '' ) ){
		
			$style = $this->append_attribute( $style, 'list-style-type: '.$ol_style.';' );
		}
		
		if( strlen( $style ) ){
			$html .= ' style="'.$style.'"';
		}
		
		$html .= '>';
		
		if( $items = $this->format_attribute( '', 'items', $attributes, false, '' ) ){
		
			for( $cI = 1; $cI <= $items; $cI++ ){
				$html .= '<li>';
				
				if( $item_name = $this->format_attribute( '', 'item_'.$cI.'_name', $attributes, false, '' ) ){
					$html .= $item_name;
				}
				
				$html .= '</li>';
			}
		}
		
		$html .= '</ol>';
		
		$html .= '</div>';
		
		return $this->format_shortcode_output( $html );
	}
}

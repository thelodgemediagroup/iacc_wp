<?php
class ElementUpload extends ElementBase {

	public function __construct($page, $section, $params = array()) {
		if (!empty($params)) {
			if (!isset($params['id'])) {
				$params['id'] = $params['name'];
				//$myname=$params['name'];
			}
			if (!isset($params['class'])) {
				$params['class'] = 'regular-text';
			}
			if (!isset($params['button_text'])) {
				$params['button_text'] = __('Insert image', TEMPLATENAME);
			}
		}
		parent::__construct($page, $section, $params);
	}

	public function render($args) {
		
			
		 $option = stripslashes(get_option($this->name, '')); 
		  
		 ?>
		
		<div id="<?php echo $args['id']; ?>_preview" class="options-image-preview">
		<?php if (!empty($option)): ?>
		<a class="thickbox" href="<?php echo $option; ?>" target="_blank"><img src="<?php echo $option; ?>"/></a>
		<?php endif; ?>
		</div>
		<input name="<?php echo $this->name; ?>" id="<?php echo $args['id']; ?>" class="<?php echo $args['class']; ?>" size="<?php echo $args['size']; ?>" value="<?php echo $option; ?>" />
		<?php if (!empty($args['desc'])) : ?>
			<span class="description"><?php echo $args['desc']; ?></span>
		<?php endif; ?>
		<div class="theme-upload-buttons">
			<a  class="thickbox button theme-upload-button" id="<?php echo $args['id']; ?>" href="media-upload.php?post_ID=0&target=<?php echo $args['id']; ?>&option_image_upload=1&type=image&TB_iframe=1&width=640&height=644"><?php echo $args['button_text']; ?></a>
		</div>
			
<?php
		
	}
}


function option_image_upload_tabs ($tabs) {
	unset($tabs['type_url'], $tabs['gallery']);
	 return $tabs;
}

function option_image_form_url($form_action_url, $type){
	$form_action_url = $form_action_url.'&option_image_upload=1&target='.$_GET['target'];
	return $form_action_url;
}

function disable_option_flash_uploader($flash){
	return false;
}

function option_image_attachment_fields_to_edit($form_fields, $post, $params ){
	unset($form_fields);
	$filename = basename( $post->guid );
	$attachment_id = $post->ID;
	
	

	$form_fields['buttons'] = array(
		'tr' => "\t\t<tr><td></td><td><input type='button' class='button' onclick='mediaUploader.OptionUploaderUseThisImage(".$post->ID.",\"". $_GET['target']."\")' value='" . __( 'Use this' , TEMPLATENAME ) . "' /> </td></tr>\n"
	);
	
	return $form_fields;
}




function  be_attachment_field_credit( $form_fields, $post, $args=array()) {
	
	$targ=$_GET['target'];
	

	
	unset($form_fields);
	$filename = basename( $post->guid );
	$attachment_id = $post->ID;
		
		?>
			
			
			<?php
		

		$form_fields['buttons'] = array(
		'tr' => "\t\t<tr><td></td><td><input type='button' class='button' onclick='mediaUploader.OptionUploaderUseThisImage(".$post->ID.",\"".$targ."\")' value='" . __( 'Use this' , TEMPLATENAME ) . "' /> </td></tr>\n"
	);
		
		
	
		?>
			
		<?php
	    return $form_fields;
	}
	 
	function allow_img_insertion($vars) {
    $vars['send'] = true; // 'send' as in "Send to Editor"
    return($vars);
}
	
	

	
function browser_uploader_default() {
		global $flash;
			
        return $flash = false;
}




function media_upload_html_bypass_new() {
	?>
	<p class="upload-html-bypass hide-if-no-js">
 
	</p>
	<script type="text/javascript">
		jQuery(".upload-html-bypass").remove();
	</script>
	<?php
}




function option_image_upload_init(){
	

	
	add_filter('flash_uploader', 'disable_option_flash_uploader');
		
	add_filter('media_upload_tabs', 'option_image_upload_tabs');
	
	add_filter( 'attachment_fields_to_edit', 'be_attachment_field_credit', 10, 4 );
	add_filter('get_media_item_args', 'allow_img_insertion');
	add_filter('media_upload_form_url', 'option_image_form_url', 10, 3);
	
	add_action('post-html-upload-ui', 'media_upload_html_bypass_new');
	wp_enqueue_script('media-uploader', get_template_directory_uri() . '/functions/admin_options/js/media-uploader.js');
	
	
	//add_filter('flash_uploader', 'disable_option_flash_uploader');
	//add_filter('media_upload_tabs', 'option_image_upload_tabs');
	//add_filter('attachment_fields_to_edit', 'option_image_attachment_fields_to_edit', 10, 2);
	//add_filter('get_media_item_args', 'allow_img_insertion');
	//add_filter('media_upload_form_url', 'option_image_form_url', 10, 2);
//	wp_enqueue_script('media-uploader', get_template_directory_uri() . '/functions/admin_options/js/media-uploader.js');
	
	
	
	
}

if (isset($_GET['option_image_upload']) || isset($_POST['option_image_upload'])) {
	add_action('admin_init', 'option_image_upload_init');
}


function option_get_image_action_callback() {
	$original = wp_get_attachment_image_src($_POST['id'],'full');
	if (! empty($original)) {
		echo $original[0];
	} else {
		die(0);
	}
	die();
}
add_action('wp_ajax_theme-option-get-image', 'option_get_image_action_callback');

?>

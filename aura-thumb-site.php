<?php
/*
Plugin Name: Aura Thumb Site
Plugin URI: http://www.auranet.com.br/plugins/aura-thumb-site/
Description: Aura Thumb Site cria uma visualização automática de suas urls incluídas em uma página ou post. Visualização de miniatura em tempo real de um site a partir de sua url.
Version: 1.0.1
Author: Auceli Neto
*/
define('ATSURLPLUGIN', plugin_dir_url( __FILE__ ));
function ATS_sc_mshot () {
	$option = str_replace(',,', ',', get_option('aura_thumb_site_url'));
	$urls = explode(',', $option);
	$output = '<div class="wrap">';
	foreach($urls as $url){
	$width = '';
		if(strlen($url)>10){
			$imageUrl = ATS_mshot($url, $width);
			$image = '<img src="' . $imageUrl . '" alt="' . $url . '" class="img"/>';
			$output .= '<div class="browsershot mshot">
						<a target="_new" title="'.esc_html(__('Visite', 'aurathumbsite')).': '.$url.'" href="' . $url . '">' . $image . '</a><br>' . str_replace('http://','',$url) . '
						</div>';
		}			
	}
	$output .= '</div>';	
	add_action('wp_head', 'ATS_style_and_script_footer', 0, 100);
	return $output;
}

function ATS_mshot ($url, $width) {
	if ($url != '') {
		return 'http://s.wordpress.com/mshots/v1/' . urlencode(clean_url($url)) . '?w=' . $width;
	} else {
		return '<img alt="Aura Thumb Site" src="https://www.auranet.com.br/fotos/aura-logo1.png" width="'.$width.'">';
	}	
}
add_shortcode('aura_thumb', 'ATS_sc_mshot');

add_action('init', 'ATS_lang_init');
function ATS_lang_init() {
    $path = dirname(plugin_basename( __FILE__ )) . '/languages/';
    $loaded = load_plugin_textdomain( 'aurathumbsite', false, $path);
    if (isset($_GET['page']) == basename(__FILE__) && !$loaded) {          
        $msg = '<div class="error">Idioma: ' . esc_html(__('Não é possível localizar o arquivo: ' . $path, 'aurathumbsite')) . '</div>';
        return $msg;
    } 
} 

add_action('admin_init', 'ATS_reg_function' );
//add_action('wp_head', 'aura_thumb_site_head');
add_action('admin_menu', 'ATS_page_menu');
function ATS_page_menu() {
	$page =	add_options_page('Aura Thumb Site', 'Aura Thumb Site', 'manage_options', 'ATS_thumb_site_menu', 'ATS_thumb_site_options_page') ;
}
function ATS_reg_function() {
	register_setting('aura-settings-group', 'aura_thumb_site_backcolor' );
	register_setting('aura-settings-group', 'aura_thumb_site_effect' );
	register_setting('aura-settings-group', 'aura_thumb_site_speed' );
	register_setting('aura-settings-group', 'aura_thumb_site_opacity' );
	register_setting('aura-settings-group', 'aura_thumb_site_img_width' );
	register_setting('aura-settings-group', 'aura_thumb_site_img_border' );
	register_setting('aura-settings-group', 'aura_thumb_site_border_color' );
	register_setting('aura-settings-group', 'aura_thumb_site_img_space' );
	register_setting('aura-settings-group', 'aura_thumb_site_title_size');
}

add_action('wp_ajax_update_url', 'ATS_update_urls');	
function ATS_update_urls(){
	$exists = get_option('aura_thumb_site_url') == '' ? '' : get_option('aura_thumb_site_url');
	update_option('aura_thumb_site_url', str_replace(',,', ',', $exists), false);
	$url = isset($_POST['url']) ? esc_url($_POST['url']) : '';
	$remove = isset($_POST['remove']) ? true : false;	
	if($remove == true){
		update_option('aura_thumb_site_url', str_replace($url.',', '', $exists), false);
	}else{
		update_option('aura_thumb_site_url', $url.','.$exists, false);		
	}			
}	

function ATS_thumb_site_options_page(){?>
<form method="post" action="options.php">
    <?php settings_fields('aura-settings-group' ); ?>
<div class="wrap">
<div id="icon-tools" class="icon32"><br /></div>
<h2>Aura Thumb Site</h2>
<hr>
<h3><?php echo esc_html(__('Configurações Gerais', 'aurathumbsite'));?></h3>
 <table width="200" class="form-table">
  <tr valign="top">
  <td width="100" scope="row"><div align="left"><?php echo esc_html(__('Cor do Fundo', 'aurathumbsite'));?></div></td>
  <td width="441" align="left"><input type="text" class="color" name="aura_thumb_site_backcolor" value="<?php echo get_option('aura_thumb_site_backcolor')?>"  />
    <i><?php echo esc_html(__('Clique para selecionar a cor', 'aurathumbsite'));?></i></td>
</tr>

<tr valign="top">
  <td scope="row"><?php echo esc_html(__('Cor da Borda', 'aurathumbsite'));?></td>
  <td align="left">
  <input type="text" class="color" name="aura_thumb_site_border_color" value="<?php echo get_option('aura_thumb_site_border_color')?>"  />
  <i><?php echo esc_html(__('Clique para selecionar a cor', 'aurathumbsite'));?></i>
  </td>
</tr>
<tr valign="top">
  <td scope="row"><?php echo esc_html(__('Espessura da Borda', 'aurathumbsite'));?></td>
  <td align="left">
     <?php $bordersize = get_option('aura_thumb_site_img_border') == '' ? 2 : get_option('aura_thumb_site_img_border')?>
  0 <input type="range" min="0" max="50" value="<?php echo $bordersize?>" class="slider" id="border"><span id="val_border"><?php echo $bordersize ?></span>
  <input type="hidden" align="absmiddle" name="aura_thumb_site_img_border" value="<?php echo $bordersize ;?>"  />
     <i>(<?php echo esc_html(__('Padrão 2px', 'aurathumbsite'))?>)</i></td>
</tr>
<tr valign="top">
  <td scope="row"><?php echo esc_html(__('Largura das Imagens', 'aurathumbsite'));?></td>
  <td align="left"><?php $width = get_option('aura_thumb_site_img_width') == '' ? 200 : get_option('aura_thumb_site_img_width')?>
  50 <input type="range" align="absmiddle" min="50" max="400" value="<?php echo $width?>" class="slider" id="width"><span id="val_width"><?php echo $width?></span>
  <input type="hidden" name="aura_thumb_site_img_width" value="<?php echo $width;?>"  />
     <i>(<?php echo esc_html(__('Padrão 200px', 'aurathumbsite'))?>)</i></td>
</tr>
<tr valign="top">
  <td scope="row"><?php echo esc_html(__('Espaço entre imagens', 'aurathumbsite'));?></td>
  <td align="left">
     <?php $image_space = get_option('aura_thumb_site_img_space') == '' ? 2 : get_option('aura_thumb_site_img_space')?>
  0 <input type="range" min="0" max="50" value="<?php echo $image_space?>" class="slider" id="space"><span id="val_space"><?php echo $image_space ?></span>
  <input type="hidden" align="absmiddle" name="aura_thumb_site_img_space" value="<?php echo $image_space ;?>"  />
     <i>(<?php echo esc_html(__('Padrão 2px', 'aurathumbsite'))?>)</i></td>
</tr>
<tr valign="top">
  <td scope="row"><?php echo esc_html(__('Tamanho da fonte', 'aurathumbsite'));?></td>
  <td align="left">
     <?php $title_size = get_option('aura_thumb_site_title_size') == '' ? 14 : get_option('aura_thumb_site_title_size')?>
  5 <input type="range" min="5" max="100" value="<?php echo $title_size?>" class="slider" id="size"><span id="val_size"><?php echo $title_size ?></span>
  <input type="hidden" align="absmiddle" name="aura_thumb_site_title_size" value="<?php echo $title_size;?>"  />
     <i>(<?php echo esc_html(__('Padrão 14px', 'aurathumbsite'))?>)</i></td>
</tr>
 </table>
    <p class="submit">
    <input type="submit" class="button" value="<?php echo esc_html(__('Salvar Alterações', 'aurathumbsite')); ?>" />
    </p>
</div>
</form>
<hr>
<h3><?php echo esc_html(__('Inclusão de Urls', 'aurathumbsite'));?></h3>
<form id="update_urls" method="POST"></form>
<table class="form-table">
	<tr valign="top">
	<td scope="row"><?php echo esc_html(__('Urls', 'aurathumbsite'));?></td>
		<td align="left"><div id="newRow"></div>
		<?php
		$option = substr(get_option('aura_thumb_site_url'), 0,-1);
		$urls = explode(',', $option);
		foreach($urls as $url){
			if($url){
			$key++;
				echo '<div class="row"><span class="remove" title="'. esc_html(__('Remover url', 'aurathumbsite')).'" id="'.$url.'">X</span> '.$key .' - '. $url.'</div>';
			}
		}
		?>
		<div id="inputFormRow">
			<input type="text" name="url" class="url" value="" placeholder="http://"/>
		</div>	
			<input type="button" value="<?php echo esc_html(__('Adicionar', 'aurathumbsite'));?>" id="addRow" name="addRow" class="button-primary">
		</td>
	</tr>	
	</table>	
</form><?php
}	
function ATS_style_and_script_admin_outher($hook) {
     wp_enqueue_script( 'script-jscolor', ATSURLPLUGIN . 'js/jscolor/jscolor.js', array(), '1.0.0', true );
	 wp_enqueue_script( 'script-aura-ats', ATSURLPLUGIN . 'js/ats_script.js', array(), '1.0.0', true );
	 wp_enqueue_style( 'style-aura-ats', ATSURLPLUGIN . 'css/ats_style.css', array());
}
add_action( 'admin_enqueue_scripts', 'ATS_style_and_script_admin_outher' );

function ats_styles_thumb() {
	$width = get_option('aura_thumb_site_img_width')==''? 200 : get_option('aura_thumb_site_img_width');
	$bgcolor = get_option('aura_thumb_site_backcolor');
	$border_height = get_option('aura_thumb_site_img_border')==''? 2 : get_option('aura_thumb_site_img_border');
	$border_color = get_option('aura_thumb_site_border_color')==''? 'ccc' : get_option('aura_thumb_site_border_color');
	$image_space = get_option('aura_thumb_site_img_space')==''? 2 : get_option('aura_thumb_site_img_space');
	$title_size = get_option('aura_thumb_site_title_size')==''? 14 : get_option('aura_thumb_site_title_size');
    wp_enqueue_style(
        'ats-aura-style',
        plugin_dir_url((__FILE__)) . 'css/ats_style.css'
    );
    $custom_css = '.mshot{font-size:'. $title_size.'px; padding:'. $image_space.'px;background-color:#'.$bgcolor.';float:left;margin:10px;border:'. 		$border_height.'px #'.$border_color.' solid;}
	.img{width:'. $width.'px}';
        wp_add_inline_style( 'ats-aura-style', $custom_css );
}
add_action( 'wp_enqueue_scripts', 'ats_styles_thumb');
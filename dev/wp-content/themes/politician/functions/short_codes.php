<?php
// Buttons shortcode
function button_shortcode($atts, $content, $code) {
	extract(shortcode_atts(array(
		'url' => '#',
		'title' => '',
		'color' => 'black',
		'target' => '_self',
	), $atts));
	$class = $code;
	$class .= ' '.$color ;
	return "<a href=\"{$url}\" target=\"{$target}\" title=\"{$title}\" class=\"{$class}\">{$content}</a>";
}
add_shortcode('button', 'button_shortcode');

// Image Shortcode
function img_shortcode($atts, $content, $code) {
	extract(shortcode_atts(array(
		'align' => '',
		'w' => 0,
		'h' => 0,
		'alt' => '',
		'title' => '',
		'mtop' => '',
		'mright' => '',
		'mbottom' => '',
		'mleft' => '',
		'url' => '',
	), $atts));
	if (empty($content))
		return;
	$styles = array();
	if ($align == 'left') {
		$styles['float'] = 'left';
		$styles['margin-right'] = '1.5em';
		$styles['margin-bottom'] = '0.5em';
	} elseif ($align == 'right') {
		$styles['float'] = 'right';
		$styles['margin-left'] = '1.5em';
		$styles['margin-bottom'] = '0.5em';
	} elseif ($align == 'center') {
		$styles['display'] = 'block';
		$styles['margin'] = '0 auto 1.5em';
	}
	if (!empty($mtop) && is_numeric($mtop)) {
		$styles['margin-top'] = $mtop.'px';
	}
	if (!empty($mbottom) && is_numeric($mbottom)) {
		$styles['margin-bottom'] = $mbottom.'px';
	}
	if (!empty($mright) && is_numeric($mright)) {
		$styles['margin-right'] = $mright.'px';
	}
	if (!empty($mleft) && is_numeric($mleft)) {
		$styles['margin-left'] = $mleft.'px';
	}
	$src = get_template_directory_uri() . "/timthumb.php?src={$content}";
	if (!empty($w))
		$src .= "&amp;w={$w}";
	if (!empty($h))
		$src .= "&amp;h={$h}";
	$src .= "&amp;zc=1";
	
	$style = '';
	foreach ($styles as $key => $val) {
		$style .= $key.': '.$val.'; ';
	}
	if (!empty($url))
		$out = "<a href=\"{$url}\" target=\"_blank\">";
	$out = "<img class=\"custom-frame\" src=\"{$src}\"";
	$out .= "style=\"{$style}\"";
	$out .= " alt=\"{$alt}\"";
	if (!empty($title))
		$out .= " title=\"{$title}\"";
	$out .= " width=\"{$w}\"";
	$out .= " height=\"{$h}\"";
	$out .= ' />';
	if (!empty($url))
		$out .= "</a>";
	return $out;
}
add_shortcode('img', 'img_shortcode');

// headings h1 - h6
function heading_shortcode($atts, $content, $code) {
	return "<{$code}  class=\"widget-title\">{$content}</{$code}>";
}
add_shortcode('h1', 'heading_shortcode');
add_shortcode('h2', 'heading_shortcode');
add_shortcode('h3', 'heading_shortcode');
add_shortcode('h4', 'heading_shortcode');
add_shortcode('h5', 'heading_shortcode');
add_shortcode('h6', 'heading_shortcode');

// tabs shortcode
function tabs_shortcode($atts, $content = null, $code) {
	extract(shortcode_atts(array(
		'style' => false
	), $atts));

	if (!preg_match_all("/(.?)\[(tab)\b(.*?)(?:(\/))?\](?:(.+?)\[\/tab\])?(.?)/s", $content, $matches)) {
		return do_shortcode($content);
	} else {
		global $tabs_counter;

		if (!isset($tabs_counter)) {
			$tabs_counter = 0;
		}
		$tabs_counter++;

		for($i = 0; $i < count($matches[0]); $i++) {
			$matches[3][$i] = shortcode_parse_atts($matches[3][$i]);
		}
		$output = '<ul class="tabs-nav">';

		for($i = 0; $i < count($matches[0]); $i++) {
			$output .= "<li><a href=\"#tab-{$tabs_counter}-{$i}\">" . $matches[3][$i]['title'] . '</a></li>';
		}
		$output .= '</ul>';
		$output .= '<div class="tabs-container">';
		for($i = 0; $i < count($matches[0]); $i++) {
			$output .= "<div id=\"tab-{$tabs_counter}-{$i}\" class=\"tab-content\">" . do_shortcode(trim($matches[5][$i])) . '</div>';
		}
		$output .= '</div>';

		return "<div class=\"content-tabs\">{$output}</div>";
	}
}
add_shortcode('tabs', 'tabs_shortcode');

// toggle block
function toggle_shortcode($atts, $content, $code) {
	extract(shortcode_atts(array(
		'title' => '',
	), $atts));
	$content = do_shortcode($content);
	return "<div class=\"box-toggle\"><span class=\"trigger\">{$title}</span><div class=\"toggle-container\">{$content}</div></div>";
}
add_shortcode('toggle', 'toggle_shortcode');

// accordion shortcode
function accordion_shortcode($atts, $content, $code) {
	extract(shortcode_atts(array(
		'title' => '',
	), $atts));
	$content = do_shortcode($content);
	return "<span class=\"acc-trigger\"><a href=\"#\">{$title}</a></span><div class=\"acc-container\"><div class=\"content\">{$content}</div></div>";
}
add_shortcode('accordion', 'accordion_shortcode');

// lists
function list_shortcode($atts, $content, $code) {
	extract(shortcode_atts(array(
		'style' => 'ordered',
		'type' => 'type1',
	), $atts));
	$items = explode("\r\n", $content);
	$out = '';
	if (!empty($items)) {
		$out = "<ul class=\"{$style} {$type}\">";
		foreach ($items as $item) {
			if (empty($item))
				continue;
			$out .= "<li>{$item}</li>";
		}
		$out .= "</ul>";
	}
	return $out;
}
add_shortcode('list', 'list_shortcode');

// quote
function quotetext_shortcode($atts, $content, $code) {
	extract(shortcode_atts(array(
		'w' => '',
		'align' => 'left',
		'top' => '0',
		'right' => '0',
		'bottom' => '0',
		'left' => '0',
	), $atts));
	if (!empty($w)) {
		$w = "width: {$w}px;";
	}
	return "<div class='align{$align}' style='{$w} margin:{$top}px {$right}px {$bottom}px {$left}px;'><blockquote><p>{$content}</p></blockquote></div>";
}
add_shortcode('quote', 'quotetext_shortcode');

// code
function code_shortcode($atts, $content, $code) {
	$content = htmlentities2($content);
	return "<code>$content</code>";
}
add_shortcode('code_block', 'code_shortcode');

// clearfix
function clear_shortcode($atts, $content, $code) {
	return "<div class=\"clear\"></div>";
}
add_shortcode('clear', 'clear_shortcode');

// divider
function divider_shortcode($atts, $content, $code) {
	return "<div class=\"divider\"></div>";
}
add_shortcode('divider', 'divider_shortcode');

// testimonial box
function testimonial_shortcode($atts, $content, $code) {
	extract(shortcode_atts(array(
		'author' => '',
	), $atts));
	$out = "<div class=\"bubble_box\">$content</div><div class=\"bubble_corner\"></div>";
	if (!empty($author)) {
		$out .= "<span class=\"testi_author\"><strong>{$author}</strong></span>";
	}
	return $out;
}
add_shortcode('testimonial', 'testimonial_shortcode');

// info boxes
function info_boxes_shortcode($atts, $content, $code) {
	return "<div class=\"{$code}\">{$content}</div>";
}
add_shortcode('error', 'info_boxes_shortcode');
add_shortcode('success', 'info_boxes_shortcode');
add_shortcode('info', 'info_boxes_shortcode');
add_shortcode('notice', 'info_boxes_shortcode');

// TAG <pre>
function pre_shortcode($atts, $content, $code) {
	return "<pre>{$content}</pre>";
}
add_shortcode('pre', 'pre_shortcode');

// columns
function columns_shortcode($atts, $content, $code) {
	global $short_code_row;
	$short_code_row++;
	extract(shortcode_atts(array(
		'indent' => 25,
		'top' => '',
		'bottom' => '',
	), $atts));
	$content = do_shortcode($content);
	$styles = array();
	if (!empty($top)) {
		$styles['margin-top'] = $top.'px';
	}
	if (!empty($bottom)) {
		$styles['margin-bottom'] = $bottom.'px';
	}
	$style = '';
	foreach($styles as $key => $val) {
		$style .= $key.': '.$val.'; ';
	}
	if (!empty($style))
		$style = "style=\"{$style}\"";
	return "<div class=\"auto-row-{$short_code_row}\" {$style}>
	{$content}
	<div class=\"clear\"></div>
</div>
<script type=\"text/javascript\">
	jQuery('.auto-row-{$short_code_row}').autoColumn({$indent}, 'div.auto-column');
</script>";
}
add_shortcode('columns', 'columns_shortcode');

// column
function column_shortcode($atts, $content, $code) {
	extract(shortcode_atts(array(
		'places' => 1,
	), $atts));
	$content = do_shortcode($content);
	return "<div data-place=\"{$places}\" class=\"auto-column\">{$content}</div>";
}
add_shortcode('column', 'column_shortcode');

// dropcap
function dropcap_shortcode($atts, $content, $code) {
	$content = do_shortcode($content);
	return "<p class=\"dropcap\">{$content}</p>";
}
add_shortcode('dropcap', 'dropcap_shortcode');

// formatter [raw] (clears wordpress default additional unnecessary tags)
function my_formatter($content) {
		 $new_content = '';
		 $pattern_full = '{(\[raw\].*?\[/raw\])}is';
		 $pattern_contents = '{\[raw\](.*?)\[/raw\]}is';
		 $pieces = preg_split($pattern_full, $content, -1, PREG_SPLIT_DELIM_CAPTURE);

		 foreach ($pieces as $piece) {
					if (preg_match($pattern_contents, $piece, $matches)) {
							  $new_content .= $matches[1];
					} else {
							  $new_content .= wptexturize(wpautop($piece));
					}
		 }

		 return $new_content;
}
remove_filter('the_content', 'wpautop');
remove_filter('the_content', 'wptexturize');
add_filter('the_content', 'my_formatter', 99);

add_filter('widget_text', 'do_shortcode');

// highlight
function highlight_shortcode($atts, $content, $code) {
	extract(shortcode_atts(array(
		'type' => 'highlight1',
	), $atts));
	$content = do_shortcode($content);
	return "<span class=\"{$type}\">{$content}</span>";
}
add_shortcode('highlight', 'highlight_shortcode');

// recent posts
function recent_posts_shortcode($atts, $content = null, $code){
	extract(shortcode_atts(array(
		'orderby' => 'date',
		'order' => 'desc',
		'posts' => 5,
		'category' => ''
	), $atts));

	$args = array(
		'orderby' => $orderby,
		'order' => $order,
		'posts_per_page' => $posts,
		'category_name' => $category
	);

	$loop = new WP_Query($args);
	ob_start();

	$_readmore_text = get_option('blog_more_text');
?>
<?php if ($loop->have_posts()): ?>
	<?php while($loop->have_posts()): $loop->the_post();
	?>
			<article class="post-item clearfix">
				<a href="<?php the_permalink(); ?>"><h3 class="title"><?php the_title(); ?></h3></a>
				<section class="post-meta clearfix">
					<div class="post-date"><?php echo get_the_date($d); ?></div>
					<div class="post-tags"><?php echo get_the_category_list(', '); ?></div>
					<div class="post-comments"><a href="<?php echo get_comments_link(); ?>" title="<?php comments_number(); ?>"><?php comments_number(); ?></a></div>
					<div class="post-tags"><?php the_tags();  ?></div>
				</section>
				<?php if(has_post_thumbnail()): ?>
				<a href="<?php the_permalink(); ?>">
					<?php the_post_thumbnail('blog', array('title' => false, 'class' => 'custom-frame')); ?>
				</a>
				<?php endif; ?>
				<?php the_excerpt(); ?>
				<?php if (!empty($_readmore_text)): ?>
				<a href="<?php the_permalink(); ?>" class="button gray" title="<?php echo $_readmore_text; ?>"><?php echo $_readmore_text; ?></a>
				<?php endif; ?>
			</article>
	<?php endwhile; ?>
<?php endif; ?>
<?php
	$out = ob_get_contents();
	ob_end_clean();

	return $out;
}

add_shortcode('recent-posts', 'recent_posts_shortcode');

?>
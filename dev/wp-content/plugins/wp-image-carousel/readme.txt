=== WP Image Carousel ===
Contributors:       Ziv, itzoovim
Plugin Name:       WP Image Carousel
Plugin URI:       http://en.itzoovim.com/plugins/wordpress-image-carousel/
Tags:              image carousel, image slider, image changer, jcarousel, wpic, WP Image  
Carousel,shortcode,image scroller,scroller
Author URI:        http://en.itzoovim.com/
Donate link:       http://en.itzoovim.com/plugins/wordpress-image-carousel/
Requires at least: 3.0 
Tested up to:      3.5.1
Stable tag:        1.0
Version:           1.0
License: GPLv3 or later

WP Image Carousel lets you easily add stylish, highly customizable image carousels  
to your site.

== Description ==

<p>WP Image Carousel (WPIC) lets you create image carousels by using a  
shortcode.</p>

<p>WPIC includes an options panel which lets you add default settings to your  
shortcode for easier and even quicker use.</p>

<strong>Features</strong>:
<ul>
	<li>10 different button colors</li>
	<li>Highly customizable</li>
    <li>Shortcode</li>
	<li>Lightweight</li>
	<li>Can be integreated with any site</li>
	<li>Hackable</li>
    <li>Active support through the wordpress forums</li>
</ul>

<p>This plugin utilizes the jquery plugin <a  
href="http://www.gmarwaha.com/jquery/jcarousellite/">jcarousel lite</a> to create  
the image carousels. This plugin also makes a call to the google scripts api to get  
jquery instead of load it from your site. This makes the plugin load faster on your  
pages and consume less bandwidth.</p>

<p>You can view a sample <a href="http://en.itzoovim.com/plugins/wordpress-image-carousel/#sample_wordpres_image_carousel">wordpress image carousel</a>  
by visiting this link.</p>

<p>The following video is a demo of WPIC. The demo shows how default settings are  
used and how the carousel can be created.</p>
[youtube http://www.youtube.com/watch?v=PAhVWD0tnu0]
== Installation ==

The automatic plugin installer should work for most people. Manual installation is  
easy and takes fewer than five minutes.

1. Download the plugin, unpack it and upload the '<em>wpic</em>' folder to your  
wp-content/plugins directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to Settings -> WPIC Options to configure the default options.

Now for the real question, <strong>how do I use this plugin?</strong>

First of all go to the plugin's options panel, and set up your default settings. Then  
copy the shortcode displayed at the top of the options page to your page/post. Click  
between the begging of the shortcode, and the end of it which will place your cursor  
at the following position:
`[wpic]SOMEWHERE HERE![/wpic]`
Then click on the "Add Media" button and select the images that you want the  
carousel to display by  holding the control key, and click on the images. Now take a  
look at the size of your images in the bottom-right corner. Make sure you select the  
same size for each image you would like to display. Take note of the this size. Hit the  
insert into post button, and wait for the images to display. As a reminder, the images  
should display "inside" the shortocde, between the end and the beginning. Take a  
look at the image you added, and between each image add the following two  
characters:
`/!` (leading slash and exclamation mark). Do not add the two characters before the  
first image, or after the last image. Take a look at the screenshots tab, to get a picture  
of what you have to do. You can edit the shortcode settings like changing the color  
of the buttons, or the speeds. Next, update the width and height settings of the  
shortcode so that they would match the dimensions of your images (the one we  
took note of earlier). If the dimensions of the images are the same dimensions you  
entered in the default plugin settings, then delete the width and height shortcode  
settings. (If you are having troubles with this part, do not hesitate to ask on the  
forums). Everything should be set, try publishing the post/page and viewing it. If  
everything worked, then great! If not, re-read these steps, take a look at the  
screenshots, or ask on the forums. We are going to try to replay as fast as possible.


<strong>Note</strong>
If you need ANY help with this plugin, please post a question in the plugin's forum  
so that I can help you. We are not going to create a plugin, just so that it could sit  
there with no support. So please, if you ever need any help with this plugin, create a  
thread in the forums.


== Changelog ==

= 1.0 = 
* Initial release

== Upgrade Notice ==

= 1.0 =
First release.

== Frequently Asked Questions ==

= I am seeing extra lines/spaces/enters when I try the carousel, how do I fix this? =

Try pasting the wpic shortcode and adding the images again. If it still won't work  
open a support thread in the forums.

== Screenshots ==

1. The image carousel with red buttons, and 4 150x150 images visible.
2. The post editor with the settings used to create carousel in the first screenshot.
3. The image carousel with navy buttons, and 1 300x225 image visible.
4. The post editor with the default settings which created the carousel in the third screenshot.
5. The default settings options panel.

== Requirements ==

In order to work, WPIC needs the following :

1. PHP version 5+
2. Preferably the latest version of WordPress.

== Donations ==
We are still working on a donations link at the moment. For now, leaving a nice  
rating will be appreciated as well.
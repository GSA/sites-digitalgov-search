<?php

function sites_dashboard_page_main()
{
    if (!current_user_can('manage_options'))
    {
      wp_die( __('You do not have sufficient permissions to access this page.') );
    }
    global $message; 
    if(isset($_POST['sites-search-update']))
    {
	    if(isset($_POST['sites-search-enable'])){
	    	update_blog_option(null, 'sites-advanced-search-enable', 'enabled');
	    }
	    else
	    	update_blog_option(null, 'sites-advanced-search-enable', 'disabled');
	}
	$sites_dashboard_search = get_blog_option(null, 'sites-advanced-search-enable');
    //$option=$_GET['edit'];
    ?>
	<div class="wrap" style="width:100%;">
		<div id="icon-options-general" class="icon32"><br /></div>
		<?php echo $message; ?>

		<h2><?php _e("Advanced Search Options"); ?></h2>

	    <?php
	    //global $wpdb;
	    //$wpdb->query($wpdb->prepare("DELETE FROM $wpdb->options WHERE option_name LIKE %s ", '_transient_feed_%'));
		?>
		<script type="text/javascript">
		jQuery( document ).ready(function($) {
			if ( $("#sites-select-search-input").val() == 'wordpress')
			{
				$('#digitalgov-search-id').hide();
			}
			$("#sites-select-search-input").change(function(){
			    if( $(this).val() == 'digitalgov')
			    	$('#digitalgov-search-id').slideDown(600);
			    else
			    	$('#digitalgov-search-id').slideUp(800);
			});
		});
		</script>
		<style type="text/css">
			#message {max-width:820px;}
			.sites-dashboard-container {background:#fff;display:inline-block;vertical-align:top;margin:0 10px 10px 0;width:325px;border:1px solid #E5E5E5;
			box-shadow: 2px 2px 2px rgba(0,0,0,0.4);
				-moz-box-shadow: 2px 2px 2px rgba(0,0,0,0.4);
				-webkit-box-shadow: 2px 2px 2px rgba(0,0,0,0.4);}
			.sites-dashboard-container .metabox-holder {width:325px;}
			.sites-dashboard-container .metabox-holder .postbox {
				min-height:250px;
				border: 0;
				/*
				box-shadow: 2px 2px 2px rgba(0,0,0,0.4);
				-moz-box-shadow: 2px 2px 2px rgba(0,0,0,0.4);
				-webkit-box-shadow: 2px 2px 2px rgba(0,0,0,0.4);
				*/
				margin: 0;
			}
			.sites-dashboard-container .metabox-holder .postbox .inside {position: initial;margin: 11px 0 35px;}
			.sites-dashboard-container .metabox-holder .postbox .inside>p:last-child {position:absolute;bottom:15px;}
			.sites-dashboard-container .search-addons-text {padding: 0 10px 10px;margin-top: -20px;}
			.sites-dashboard-container .search-addons-text p {margin: 0;}
		</style>
		<div class="sites-dashboard-container">
			<form method="post" action="options.php">
				<?php settings_fields( 'sites-select-search-group' ); ?>
				<?php
				$option = 'sites-select-search';
				$options = get_blog_option(null, $option);
				?>
				
			    <div class="metabox-holder">
			        <div class="postbox">
			        <h3><?php _e("Set Default Search Engine", 'sites_dashboard_select_search'); ?></h3>
			        
			            <div id="general" class="inside" style="padding: 10px;">
			            	<p style="margin-top:0;">
			            		<span style="display: inline-block;width: 125px;">Search engine:</span>
								<select id="sites-select-search-input" name="<?php echo $option; ?>[sites-select-search-status]">
								  <option value="wordpress">Wordpress</option>
								  <option value="digitalgov" <?php if($options['sites-select-search-status'] == 'digitalgov'){echo 'selected';} ?>>DigitalGov</option>
								</select><br/>
							</p>
							<p id="digitalgov-search-id"><span style="display: inline-block;width: 125px;margin:10px 0;">DigitalGov Search handle:</span><br/> <input type="text" name="<?php echo $option;?>[sites-select-search-id]" value="<?php echo $options['sites-select-search-id'];?>" /></p>
			                <p><input type="submit" class="button" value="<?php _e('Save Settings') ?>" /></p>
			            </div>
			        </div>
			        <div class="search-addons-text">
			    	<p>Wordpress offers a very basic search as a default. <br/><br/>
To enhance your search results, select <a href="http://search.digitalgov.gov/">DigitalGov Search</a> as your search engine. <a href="http://search.usa.gov/signup">Sign up</a>, add a site and add the handle you receive via email to the box above.
</p>
					</div>
			    </div>

			    <input type="hidden" name="action" value="update" />
			    <input type="hidden" name="page_options" value="<?php echo $option; ?>" />
			</form>
		</div>
		<div class="sites-dashboard-container">
			<form method="post" action="options.php">
				<?php settings_fields( 'sites-additional-rss-types-group' ); ?>
				<?php
				$option = 'sites-additional-rss-types';
				$options = get_blog_option(null, $option);
				?>
			    <div class="metabox-holder">
			        <div class="postbox">
			        <h3><?php _e("Additional RSS Types", 'sites_dashboard_additional_rss_types'); ?></h3>
			        
			            <div id="general" class="inside" style="padding: 10px;">
							<?php $post_types = get_post_types( array('public' => true), 'names' ); ?>
							<p style="margin-top:0;">
							<?php _e("Select type of display on RSS feed", 'sites_dashboard'); ?>
								<p>&nbsp;<input type="checkbox" disabled="disabled" checked="checked" name="<?php echo $option; ?>[post]">&nbsp;Post</p>
							<?php
								foreach ( $post_types as $post_type ) {
									if (!in_array($post_type,array('post','attachment','guest-author','optionsframework')))
									{ 
										?> <p>&nbsp;<input type="checkbox" name="<?php echo $option; ?>[<?php echo $post_type; ?>]" value="Y" <?php if($options[$post_type] === 'Y'){ echo 'CHECKED';}; ?> /><?php echo " ".ucwords($post_type); ?></p>
										<?php
									}
								}
							?></p>
			                <p><input type="submit" class="button" value="<?php _e('Save Settings') ?>" /></p>
			            </div>
			        </div>
			        <div class="search-addons-text">
			        	<p>Make sure both boxes are checked if you want <span >DigitalGov Search</span> results to include posts and pages. Otherwise, only select the category you want.</p>
			       	</div>
			    </div>
			    <input type="hidden" name="action" value="update" />
			    <input type="hidden" name="page_options" value="<?php echo $option; ?>" />
			</form>
		</div>
		<div class="sites-dashboard-container">
			<form method="post" id='sites-advanced-search' action="">
			    <div class="metabox-holder">
			        <div class="postbox">
			        <h3><?php _e("Extended WordPress Search", 'sites_dashboard_advanced_search'); ?></h3>
			            <div id="general" class="inside" style="padding: 10px;">
			            	<p style="margin-top:0;">
				<?php _e("Check box to enable extended Wordpress searching functionality.", 'sites_dashboard_advanced_search'); ?>
				
				   <p>&nbsp;<input type="checkbox" name="sites-search-enable" value="enabled" <?php if($sites_dashboard_search === 'enabled'){ echo 'CHECKED';}; ?> />Enable Alternate Searching</p>
						</p>
			                <p><input type="submit" class="button" value="<?php _e('Save Settings') ?>" /></p>
			            </div>
			        </div>
			    </div>
			    <div class="search-addons-text">
			    	Check this box if you’ve selected WordPress search as your default search engine and you want it to search post tags, authors and guest authors.
			    </div>
				<input type="hidden" name="sites-search-update" value="Y" />
			</form>
		</div>
	</div>
<?php
}
?>

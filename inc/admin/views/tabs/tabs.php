<?php
/**
 * Template file for WP Travel tabs.
 *
 * @package WP Travel
 */

?>
<div class="wp-travel-tabs-wrap">
	<ul class="wp-travel-tabs-nav">
		<?php
		foreach ( $tabs as $key => $tab ) :
			$class = ( 0 === $i ) ? 'wp-travel-tab-active' : '';
		?>
		<li id="wp-travel-tab-<?php echo esc_attr( $key ); ?>"><a href="#wp-travel-tab-content-<?php echo esc_attr( $key ); ?>" class="<?php echo esc_attr( $class ); ?>"><?php echo esc_attr( $tab['tab_label'] ); ?></a></li>
		<?php endforeach; ?>

		<li id="wp-travel-tab-faq" class="ui-state-default ui-corner-top" role="tab" tabindex="-1" aria-controls="wp-travel-tab-content-faq" aria-labelledby="ui-id-7" aria-selected="false" aria-expanded="false"><a href="#wp-travel-tab-content-faq" class="wp-travel-tab-active ui-tabs-anchor" role="presentation" tabindex="-1" id="ui-id-7">FAQ</a></li>

		<li id="wp-travel-tab-setting" class="ui-state-default ui-corner-top" role="tab" tabindex="-1" aria-controls="wp-travel-tab-content-tab-setting" aria-labelledby="ui-id-7" aria-selected="false" aria-expanded="false"><a href="#wp-travel-tab-content-tab-setting" class="wp-travel-tab-active ui-tabs-anchor" role="presentation" tabindex="-1" id="ui-id-7">Tab</a></li>


	</ul>
	<div class="wp-travel-tabs-contents">
		<?php
		foreach ( $tabs as $key => $tab ) :
		?>
		<div id="wp-travel-tab-content-<?php echo esc_attr( $key ); ?>" class="ui-state-active wp-travel-tab-content">
			<h3 class="wp-travel-tab-content-title"><?php echo esc_attr( $tab['content_title'] ); ?></h3>
			<?php do_action( 'wp_travel_tabs_content_' . $collection, $key, $args ); ?>
		</div>
		<?php endforeach; ?>



		<div id="wp-travel-tab-content-faq" class="ui-state-active wp-travel-tab-content ui-tabs-panel ui-widget-content ui-corner-bottom" aria-labelledby="ui-id-5" role="tabpanel" aria-hidden="true">

			<div class="wp-travel-tab-content-faq-header">
				<div class="wp-collapse-open">
					<a href="#"><span class="open-all" id="open-all">Open All</span></a>
					<a href="#"><span class="close-all" id="close-all">Close All</span></a>
				</div>
			</div>

			<h3 class="wp-travel-tab-content-title">FAQ</h3>


	        <ul id="tab-accordion" class="tab-accordion">
	        	<li>
	        		 <h3 class="heading-accordion">
	            	<div class="sort-faq-section">
	            	</div>
					<span class="faq-title">
		            	How to sort menu item?
		            </span>

		            	<input class="section_title" id="title-1"  type="text" name="faq-question[1]" placeholder="(add question)" value="How to sort menu item?">
						
		            	<span class="dashicons dashicons-no-alt hover-icon"></span>
		            </h3>
		            <div>
		                <textarea rows="6">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</textarea>
		            </div>
	        	</li>
	           

	            <li>
	            	<h3 class="heading-accordion">
	            	<div class="sort-faq-section">
	            	</div>
					<span class="faq-title">
	            		How to reset global setting? 
	            	
	            	</span>
	            	<input class="section_title" id="title-2"  type="text" name="faq-question[2]" placeholder="(add question)" value="How to reset global setting?">
	            	<span class="dashicons dashicons-no-alt hover-icon"></span></h3>
	            <div>
	                <textarea rows="6">Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est.</textarea>
	            </div>

	            </li>
	            <li>
	            	<h3 class="heading-accordion">
	            	<div class="sort-faq-section">
	            	</div>
	            	<span class="faq-title">
	            		How to add paypal? 
	            	</span>

	            	<input class="section_title" id="title-3"  type="text" name="faq-question[3]" placeholder="(add question)" value="How to add paypal?">
	            	
	            	
	            	<span class="dashicons dashicons-no-alt hover-icon"></span></h3>
	            <div>
	                <textarea rows="6">At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga.</textarea>
	            </div>
	        </li>
	        </ul>
	        <div class="wp-travel-faq-quest-button clearfix">		
				<input type="button" value="Add New Question" class="button button-primary wp-travel-faq-add-new">		
			</div>
		</div>

		<div id="wp-travel-tab-content-tab-setting" class="ui-state-active wp-travel-tab-content ui-tabs-panel ui-widget-content ui-corner-bottom" aria-labelledby="ui-id-5" role="tabpanel" aria-hidden="true">
			<h3 class="wp-travel-tab-content-title">Tab Setting</h3>
			
		</div>




	</div>
</div>

<script type="text/javascript">
	jQuery(document).ready(function(){
	    jQuery('#tab-accordion').accordion({
		  collapsible: true,
		  animate: 100,
		});
	    jQuery( "#tab-accordion" ).sortable();
    	jQuery( "#tab-accordion" ).disableSelection();
	});  
</script>
<?php
/**
 * Fact Tab HTML.
 *
 * @package wp-travel\inc\admin\views\tabs\tab-contents\itineraries
 */

global $post;
$wp_travel_trip_facts = get_post_meta( $post->ID, 'wp_travel_trip_facts', true );
if(is_string($wp_travel_trip_facts))
    $wp_travel_trip_facts = json_decode($wp_travel_trip_facts,true);
function fact_html($label='', $custom = '',$description='', $index = false){
    ob_start();
    ?>
    <div class="panel panel-default ">
            <div class="panel-heading" role="tab" id="heading-<?php echo $index ? $index : '$index'; ?>">
                <h4 class="panel-title">
                    <div class="wp-travel-sorting-handle"></div>
                    <a role="button" data-toggle="collapse" data-parent="#accordion-fact-data" href="#collapse-<?php echo $index ? $index : '$index'; ?>" aria-expanded="true" aria-controls="collapse-<?php echo $index ? $index: '$index'; ?>">
                        <span bind="fact_question_<?php echo $index ? $index : '$index'; ?>">Fact <span>
                        <span class="collapse-icon"></span>
                    </a>
                    <span class="dashicons dashicons-no-alt hover-icon wt-accordion-close"></span>
                </h4>
            </div>
            <div id="collapse-<?php echo $index ? $index: '$index'; ?>" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading-<?php echo $index ?$index: '$index'; ?>">
                <div class="panel-body">
                    <div class="panel-wrap">
                        <label>Select label</label>
                        <select name="wp_travel_trip_facts[<?php echo $index ? $index : '$index'; ?>][label]" class="fact-type-selecter">
                        <?php foreach(apply_filters('wp_travel_trip_facts_label_options',[''=>'Select a label','option 1'=>'option 1','option 2'=>'option 2']) as $key => $option): ?>
                            <option <?php if($label == $key){echo 'selected';}?> value="<?php echo $key; ?>"><?php echo esc_html($option); ?></option>
                        <?php endforeach; ?>
                        <option <?php if($label == 'custom'){echo 'selected';}?> value="custom">Custom</option>
                    </select>
                    <input style="<?php if($label != 'custom') {echo 'display:none;';} ?> width:100%; margin-top:10px;" value="<?php echo $custom; ?>" type="text" name="wp_travel_trip_facts[<?php echo $index ?$index: '$index'; ?>][custom_label]" placeholder="Enter a custom name">
                    </div>
                    <textarea rows="6" name="wp_travel_trip_facts[<?php echo $index ? $index : '$index'; ?>][description]" placeholder="Write Your Answer."><?php echo $description; ?></textarea>
                </div>
            </div>
        </div>
        <?php
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
}

$content = fact_html();
?>
<style>
    .fact-sample{
        display:none;
    }
</style>

<div class="form-table fact-table">
 			
    <div class="fact-sample">
        <?php echo $content; ?>
    </div>
    <div id="tab-accordion" class="tab-accordion">
        <div class="panel-group wp-travel-sorting-tabs ui-sortable" id="accordion-fact-data" role="tablist" aria-multiselectable="true">
            <?php foreach($wp_travel_trip_facts as $key => $trip) : ?>
            <?php echo fact_html($trip['label'], $trip['custom_label'],$trip['description'], $key + 1); ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<div class="wp-travel-fact-quest-button clearfix">
    <input onclick="addFact()" type="button" value="Add New Question" class="button button-primary">
</div>
<script>
    const addFact = function(){
        const elem = jQuery('.fact-sample').clone().removeClass('fact-sample')
        const random = Math.random().toString(36).replace(/[^a-z]+/g, '').substr(0, 5);
        jQuery('.fact-table #accordion-fact-data')
        .append(jQuery('<div>').append(elem.wrap('<div>')
        .html()
        .split('$index')
        .join('random')));
    }
    jQuery(document).on('click','.fact-deleter', function(){
        jQuery(this).parent().parent().parent().remove();
    })

    jQuery(document).on('change','.fact-type-selecter', function(){
        const elem = jQuery(this).siblings('input');
        jQuery(this).val() == 'custom' ? elem.show() : elem.hide();
    }); 
</script>
<?php
/**
 * Fact Tab HTML.
 *
 * @package wp-travel\inc\admin\views\tabs\tab-contents\itineraries
 */

global $post;
$wp_travel_trip_facts = get_post_meta( $post->ID, 'wp_travel_trip_facts', true );
if(is_string($wp_travel_trip_facts)){
    $wp_travel_trip_facts = json_decode($wp_travel_trip_facts,true);
}
function wp_travel_fact_single($fact, $index, $setting = []){
    ?>
    <select name="wp_travel_trip_facts[<?php echo $index; ?>][value]" id="">
        <?php foreach($setting['options'] as $option):?>
            <option <?php if($option == $fact['value']) echo 'selected'; ?>  value="<?php echo $option; ?>"><?php echo $option; ?></option>
        <?php endforeach; ?>
    </select>
    <?php
}

function wp_travel_fact_multiple($fact, $index, $setting = []){
    foreach($setting['options'] as $option):
    ?>
    <input type="checkbox" <?php if(isset($fact['value']) && is_array($fact['value']) && in_array($option, $fact['value'])) echo 'checked'; ?> name="wp_travel_trip_facts[<?php echo $index; ?>][value][]" value="<?php echo $option; ?>" id=""><?php echo $option; ?></input>
    <?php
    endforeach;
}

function wp_travel_fact_text($fact, $index, $setings = []){
    ?>
        <textarea name="wp_travel_trip_facts[<?php echo $index; ?>][value]" id="" cols="30" rows="10"><?php echo $fact['value']; ?></textarea>
    <?php
}

function fact_html($fact = [], $index = false){
    $settings = get_option('wp_travel_settings');
    $settings = isset($settings['wp_travel_trip_facts_settings']) ? array_values($settings['wp_travel_trip_facts_settings']) : [];
    if($settings == []){
        return '';
    }
    $name = [];
    foreach($settings as $set){
        $name[] = $set['name'];
    }
    if(isset($fact['type']) && !in_array($fact['type'], $name)){
        return '';
    }
    ob_start();
    is_array($fact) && extract($fact);
?>
    <div class="panel panel-default ">
            <div class="panel-heading" role="tab" id="heading-<?php echo $index ? $index : '$index'; ?>">
                <h4 class="panel-title">
                    <div class="wp-travel-sorting-handle"></div>
                    <a class="<?php $index && print_r('collapsed') ?>" role="button" data-toggle="collapse" data-parent="#accordion-fact-data" href="#collapse-<?php echo $index ? $index : '$index'; ?>" aria-expanded="<?php $index ? 'false' : 'true' ?>" aria-controls="collapse-<?php echo $index ? $index: '$index'; ?>">
                        <span bind="fact_question_<?php echo $index ? $index : '$index'; ?>">Fact <span>
                        <span class="collapse-icon"></span>
                    </a>
                    <span class="dashicons dashicons-no-alt hover-icon wt-accordion-close"></span>
                </h4>
            </div>
            <div id="collapse-<?php echo $index ? $index: '$index'; ?>" class="panel-collapse collapse <?php $index ? '' : 'in' ?>" role="tabpanel" aria-labelledby="heading-<?php echo $index ?$index: '$index'; ?>">
                <div class="panel-body">
                    <div class="panel-wrap">
                        <label>Select type</label>
                        <select class="fact-type-selecter" name="<?php $index && print_r('wp_travel_trip_facts['.$index.'][type]') ?>">
                        <?php if(!isset($type)): ?>
                            <option value="">Select a label</option>
                        <?php endif; ?>    
                        <?php foreach($settings as $key => $setting): ?>
                                <option <?php if(isset($type) && $type == $setting['name']):$settingss = $setting; $selected = $setting['type']; ?> selected <?php endif; ?> value="<?php echo $setting['name']; ?>"><?php echo esc_html( $setting['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="fact-holder">
                            <?php isset($selected) && call_user_func('wp_travel_fact_'.$selected, $fact, $index, $settingss)?>
                        </div>
                    </div>
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
            <?php if(is_array($wp_travel_trip_facts)): ?>
                <?php foreach($wp_travel_trip_facts as $key => $fact) : ?>
                    <?php isset($fact['value']) && print_r(fact_html($fact, $key + 1)) ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<div class="wp-travel-fact-quest-button clearfix">
    <?php
      $settings = get_option('wp_travel_settings');
      $settings = isset($settings['wp_travel_trip_facts_settings']) ? array_values($settings['wp_travel_trip_facts_settings']) : [];
   ?>
    <?php if(count($settings) > 0) : ?>
        <input onclick="addFact()" type="button" value="Add New Question" class="button button-primary">
    <?php else: ?>
    There are no labels currently. Click <a href="<?php echo site_url('wp-admin/edit.php?post_type=itineraries&page=settings#wp-travel-tab-content-facts'); ?>">here</a> to add one.
    <?php endif; ?>
</div>
<script>
    const settings = <?php 
    $settings = get_option('wp_travel_settings');
    echo isset($settings['wp_travel_trip_facts_settings']) ? json_encode(array_values($settings['wp_travel_trip_facts_settings'])) : '[]';
    ?>;
    const addFact = function(){
        const elem = jQuery('.fact-sample').clone().removeClass('fact-sample')
        const random = Math.random().toString(36).replace(/[^a-z]+/g, '').substr(0, 5);
        jQuery('.fact-table #accordion-fact-data')
        .append(jQuery('<div>').append(elem.wrap('<div>')
        .html()
        .split('$index')
        .join(random)));
    }

    jQuery(document).on('click','.fact-deleter', function(){
        jQuery(this).parent().parent().parent().remove();
    })

    const types = {
        multiple: function(obj ,unique){
            val = this.val();
            this.siblings('.fact-holder').html((obj.options || []).map(function(option){
                return jQuery('<input type="checkbox">'+option+'</input>').attr('name','wp_travel_trip_facts['+unique+'][value][]').attr('value',option);
            }));
        },
        single: function(obj, unique){
            val = this.val();
            this.siblings('.fact-holder').html(jQuery('<select>').attr('name','wp_travel_trip_facts['+unique+'][value]').html((obj.options || []).map(function(option){
                return jQuery('<option>').attr('value',option).html(option);
            })));
        },
        text: function(obj, unique){
            val = this.val();
            this.siblings('.fact-holder').html(jQuery('<textarea>').attr('name','wp_travel_trip_facts['+unique+'][value]'));
        }
    }

    jQuery(document).on('change','.fact-type-selecter', function(){
        const  unique = Math.random().toString(36).substr(2, 9);
        jQuery(this).attr('name','wp_travel_trip_facts['+unique+'][type]');
        const val = jQuery(this).val();
        const setting = settings.filter(function(setting){
            return val == setting['name']
        })[0];
        if(setting){
            types[setting.type].call(jQuery(this),setting,unique);
        } else{

        }
    }); 
</script>
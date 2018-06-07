<?php

// Fields array.
$checkout_fields    = wp_travel_get_checkout_form_fields();
$traveller_fields   = $checkout_fields['traveller_fields'];
$billing_fields     = $checkout_fields['billing_fields'];
$payment_fields     = $checkout_fields['payment_fields'];
global $wt_cart;

print_r($wt_cart);

$form_field = new WP_Travel_FW_Field(); ?>
<form method="POST" action="" id="wp-travel-booking">
    <!-- Travellers info -->  
    <?php foreach( $trips as $cart_id => $trip ) : ?>
        <div class="wp-travel-trip-details">
            <div class="section-title text-left">
            <h3><?php echo esc_html( get_the_title( $trip['trip_id'] ) ) ?><small> / 8 days 7 nights</small></h3>
            </div>
            
            <div class="panel-group number-accordion">
                <div class="panel ws-theme-timeline-block">
                    <div class="panel-heading">            
                        <h4 class="panel-title">Your selected departure date </h4>
                    </div>
                    <div id="number-accordion1" class="panel-collapse collapse in">
                        <div class="panel-body">
                        <p>Your departure date: June 26, 2018 - June 29, 2016 <a href="#" class="btn btn-block simple">change</a></p>

                        </div>
                    </div>
                </div>
                <div class="panel ws-theme-timeline-block">
                    <div class="panel-heading">										
                        <h4 class="panel-title"><?php esc_html_e( 'Traveller Details', 'wp-travel' ) ?></h4>
                    </div>
                    <div id="number-accordion2" class="panel-collapse collapse in">
                        <div class="panel-body">
                            <div class="payment-content">
                                <div class="payment-traveller">
                                
                                    <div class="row gap-0">
                                        <div class="col-md-offset-3 col-sm-offset-4 col-sm-8 col-md-9">
                                            <h6 class="heading mt-0 mb-15">Lead Traveller</h6>
                                        </div>
                                    </div>
                                    <?php foreach( $traveller_fields as $field_group => $field ) : ?>
                                        <?php
                                        $field_name = sprintf( '%s[%s][]', $field['name'], $cart_id );
                                        $field['name'] = $field_name;                               
                                        if ( 'hidden' === $field['type'] ) {
                                            echo $form_field->init()->render_input( $field );
                                            continue;
                                        }
                                            $wrapper_class = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : ''; ?>
                                        <div class="form-horizontal <?php echo esc_attr( $wrapper_class ); ?>">
                                            <div class="form-group gap-20">
                                                <label class="col-sm-4 col-md-3 control-label"><?php echo esc_html( $field['label'] ) ?>:</label>
                                                <div class="col-sm-8 col-md-9">
                                                    <?php echo $form_field->init()->render_input( $field ); ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>                                    
                                </div>														
                            </div>
                            <div class="text-center">
                                <button class="btn btn-block simple center-div wp-travel-add-traveller" data-cart-id="<?php echo esc_attr( $cart_id ) ?>"><?php esc_html_e( 'Add another traveller', 'wp-travel' ) ?></button>
                            </div>
                        </div>
                    </div>
                </div>                                        
            </div>
        </div>
    <?php endforeach; ?>

    <!-- Billing info -->
    <div class="panel ws-theme-timeline-block">
        <div class="panel-heading">
        
        <h4 class="panel-title">Billing Address</h4>
        </div>
        <div id="number-accordion3" class="panel-collapse collapse in">
        <div class="panel-body">
            <div class="payment-content">
                <?php foreach( $billing_fields as $field_group => $field ) : ?>
                    <?php
                    if ( 'hidden' === $field['type'] ) {
                        echo $form_field->init()->render_input( $field );
                        continue;
                    }
                    $wrapper_class = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : ''; ?>               
                    <div class="form-horizontal <?php echo esc_attr( $wrapper_class ); ?>">
                        <div class="form-group gap-20">
                            <label class="col-sm-4 col-md-3 control-label"><?php echo esc_html( $field['label'] ) ?>:</label>
                            <div class="col-sm-8 col-md-9">
                                <?php echo $form_field->init()->render_input( $field ); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        </div>
    </div>
    <!-- Payment info -->
    <div class="panel ws-theme-timeline-block">
        <div class="panel-heading">
        
        <h4 class="panel-title">Finish Payment /  secure</h4>
        </div>
        <div id="number-accordion4" class="panel-collapse collapse in">
        <div class="panel-body">           
            <div class="payment-content">
                <?php foreach( $payment_fields as $field_group => $field ) : ?>
                    <?php if ( 'hidden' === $field['type'] ) {
                        echo $form_field->init()->render_input( $field );
                        continue;
                    }
                    $wrapper_class = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : ''; ?>
                    <div class="form-horizontal <?php echo esc_attr( $wrapper_class ); ?>">
                        <div class="form-group gap-20">
                            <label class="col-sm-4 col-md-3 control-label"><?php echo esc_html( $field['label'] ) ?>:</label>
                            <div class="col-sm-8 col-md-9">
                                <?php echo $form_field->init()->render_input( $field ); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>                 
                <div class="wp-travel-form-field button-field">
                    <?php wp_nonce_field( 'wp_travel_security_action', 'wp_travel_security' ); ?>
                    <input type="submit" name="wp_travel_book_now" id="wp-travel-book-now" value="Book and Pay"> 
                </div>
            </div>        
        </div>
        </div>
    </div>
</form>

<script type="text/html" id="tmpl-traveller-info">
    <?php ob_start(); ?>
    <div class="payment-traveller added" data-unique-index="{{{data.unique_index}}}">
        <a href="#" class="pull-right font12 traveller-remove"><i class="fa fa-times-circle"></i></a>
        <div class="row gap-0">
            <div class="col-md-offset-3 col-sm-offset-4 col-sm-8 col-md-9">
                <h6 class="heading mt-0 mb-15"><?php esc_html_e( 'Traveller ' ) ?><span class="traveller-index">{{data.index}}</span></h6>
            </div>
        </div>
        <?php foreach( $traveller_fields as $field_group => $field ) : ?>
            <?php
                $field_name = sprintf( '%s[{{{data.cart_id}}}][]', $field['name'] );
                $field['name'] = $field_name;

                $field_id = sprintf( '%s-{{{data.unique_index}}}', $field['id'] );
                $field['id'] = $field_id; ?>
            <div class="form-horizontal">
                <div class="form-group gap-20">
                    <label class="col-sm-4 col-md-3 control-label"><?php echo esc_html( $field['label'] ) ?>:</label>
                    <div class="col-sm-8 col-md-9">
                        <?php echo $form_field->init()->render_input( $field ); ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>          
    </div>
    <?php $content = ob_get_contents(); ob_end_clean(); echo json_encode($content) ?>
</script>
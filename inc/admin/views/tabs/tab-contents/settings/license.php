<?php
/**
 * Callback for License tab.
 *
 * @param  Array $tab  List of tabs.
 * @param  Array $args Settings arg list.
 */
function settings_callback_license( $tab, $args ) {
    do_action( 'wp_travel_license_tab_fields', $args );
    ?>
    <div class="license_wrapper">
			<div class="license_grid">
				<div class="form_field">
						<label for="key1" class="control-label label_title">license key</label>
						<div class="subject_input">
							<input type="email" class="form-control" id="key1" placeholder="New Subjects">
							<input type="submit" class="button">
							<i class="fas fa-check"></i>
							</div>
						</div>
				</div>
				<div class="license_grid">
				<div class="form_field">
						<label for="key2" class="control-label label_title">license key</label>
						<div class="subject_input">
							<input type="email" class="form-control" id="key2" placeholder="New Subjects">
							<input type="submit" class="button">
							<i class="fas fa-times"></i>
							</div>
						</div>
				</div>
				<div class="license_grid">
				<div class="form_field">
						<label for="key3" class="control-label label_title">license key</label>
						<div class="subject_input">
							<input type="email" class="form-control" id="key3" placeholder="New Subjects">
							<input type="submit" class="button">
							</div>
						</div>
				</div>
				<div class="license_grid">
				<div class="form_field">
						<label for="key4" class="control-label label_title">license key</label>
						<div class="subject_input">
							<input type="email" class="form-control" id="key4" placeholder="New Subjects">
							<input type="submit" class="button">
							</div>
						</div>
				</div>
				<div class="license_grid">
				<div class="form_field">
						<label for="key5" class="control-label label_title">license key</label>
						<div class="subject_input">
							<input type="email" class="form-control" id="key5" placeholder="New Subjects">
							<input type="submit" class="button">
							</div>
						</div>
				</div>
				<div class="license_grid">
				<div class="form_field">
						<label for="key6" class="control-label label_title">license key</label>
						<div class="subject_input">
							<input type="email" class="form-control" id="key6" placeholder="New Subjects">
							<input type="submit" class="button">
							</div>
						</div>
				</div>
    </div>
    <?php
}
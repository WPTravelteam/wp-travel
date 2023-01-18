const Header = () => {

	return(
		<div id="wp-travel-setup-page-header">
			<img id="logo" src={ _wp_travel.plugin_url + 'assets/images/wp-travel-log.png' } /><h1>{_wp_travel.plugin_name}</h1>
		</div>
	);

}

export default Header
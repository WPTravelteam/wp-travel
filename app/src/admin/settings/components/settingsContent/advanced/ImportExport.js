import { __ } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';


export default () => {

    const WP_Travel_Export = () => {
        fetch( _wp_travel.site_url + '/wp-json/wp-travel/v1/export-settings-data', {
              method: "GET"
            }).then((res) => res.json())
              .then((data) => location.reload() )
              .catch((err) => console.error(err));

    }

    const WP_Travel_Import = ( ) => {

        

        var reader = new FileReader();
        reader.readAsText(document.getElementById("fileupload").files[0]);

        reader.onload = function(e) {
            var rawLog = reader.result;

            var formdata = new FormData();
            formdata.append("settings_data", rawLog);
            fetch( _wp_travel.site_url + '/wp-json/wp-travel/v1/import-settings-data', {
              method: "POST", 
              body: formdata
            }).then((res) => res.json())
              .then((data) => location.reload() )
              .catch((err) => console.error(err));
        };
    }

    return (
        <>
            <div className="wp-travel-section-header">
                <h2 className="wp-travel-section-header-title">
                    {__("Import Export Settings", "wp-travel")}
                </h2>
                <p className="wp-travel-section-header-description">
                    {__("Additional PWA settings.", "wp-travel")}
                </p>
            </div>
            <div className='wp-travel-section-content'>
                <h3>{__("Export Settings", "wp-travel")}</h3>
                <p>{__("Click Export to export all the WP Travel settings Data.", "wp-travel")}</p>
                <button type="button" id="wp-travel-export-settings" class="components-button is-secondary" onClick={WP_Travel_Export}>{__("Export", "wp-travel")}</button>
                <br/><br/>
                <h3>{__("Import Settings", "wp-travel")}</h3>
                <input id="fileupload" type="file" name="fileupload" size="50" />
                <p>{__("Upload file and click Import file to import settings data.", "wp-travel")}</p>
                <button type="button" id="wp-travel-import-settings" class="components-button is-secondary" onClick={WP_Travel_Import}>{__("Import", "wp-travel")}</button>

            </div>
        </>
    )
}
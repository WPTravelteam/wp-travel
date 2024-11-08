import { __ } from '@wordpress/i18n';
import { PanelRow, ToggleControl } from '@wordpress/components';
import { useSelect, dispatch } from '@wordpress/data';

export default () => {

    const allData = useSelect((select) => {
        return select('WPTravel/Admin').getAllStore()
    }, []);

    const {
        enable_session,
        options
    } = allData;

    const { updateSettings } = dispatch('WPTravel/Admin');

    const WP_Travel_Export = () => {

        fetch(`${_wp_travel.site_url}/wp-json/wp-travel/v1/export-settings-data`, {
            method: "GET",
            headers: {
                'X-WP-Nonce': _wp_travel.rest_nonce // Add nonce for authentication
            }
        })
        .then((response) => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.blob(); // Convert response to a file blob
        })
        .then((blob) => {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'wp-travel-settings.json'; // Filename for download
            document.body.appendChild(a);
            a.click();
            a.remove();
            window.URL.revokeObjectURL(url); // Clean up the URL object
        })
        .catch((err) => {
            console.error("Export failed:", err);
            alert("Failed to export settings");
        });
    };
    

    const WP_Travel_Import = () => {
        const file = document.getElementById("fileupload").files[0];
        if (!file) {
            alert("Please upload a file before importing.");
            return;
        }
        var reader = new FileReader();
        reader.readAsText(file);

        reader.onload = function(e) {
            var rawLog = reader.result;

            var formdata = new FormData();
            formdata.append("settings_data", rawLog);
            fetch(_wp_travel.site_url + '/wp-json/wp-travel/v1/import-settings-data', {
              method: "POST", 
              body: formdata
            }).then((res) => res.json())
              .then((data) => {
                  alert("Settings imported successfully");
                  location.reload();
              })
              .catch((err) => {
                  console.error("Import failed:", err);
                  alert("Failed to import settings");
              });
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
                <button type="button" id="wp-travel-export-settings" className="components-button is-secondary" onClick={WP_Travel_Export}>
                    {__("Export", "wp-travel")}
                </button>
                <br/><br/>
                <h3>{__("Import Settings", "wp-travel")}</h3>
                <input id="fileupload" type="file" name="fileupload" size="50" />
                <p>{__("Upload file and click Import file to import settings data.", "wp-travel")}</p>
                <button type="button" id="wp-travel-import-settings" className="components-button is-secondary" onClick={WP_Travel_Import}>
                    {__("Import", "wp-travel")}
                </button>
            </div>
        </>
    )
}

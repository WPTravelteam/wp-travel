import {useRef} from '@wordpress/element'
import { applyFilters } from "@wordpress/hooks";
import { useSelect, dispatch } from "@wordpress/data";
import { _n, __ } from "@wordpress/i18n";
import { PanelRow, TextControl, Icon } from "@wordpress/components";
import { info } from "@wordpress/icons";
import Tooltip from '../../UI/Tooltip';

import Select from "react-select";

export default () => {
  const allData = useSelect((select) => {
    return select("WPTravel/Admin").getAllStore();
  }, []);
  const {
    wp_travel_map,
    google_map_api_key,
    google_map_zoom_level,
    options,
  } = allData;

  console.log(allData)

  const { updateSettings } = dispatch("WPTravel/Admin");

  // options
  let mapOptions = [];

  if ("undefined" != typeof options) {
    if ("undefined" != typeof options.maps) {
      mapOptions = options.maps;
    }
  }

  // selected options.
  let selectedMap = mapOptions.filter((opt) => {
    return opt.value == wp_travel_map;
  });

  let thisRef = useRef();

  return (
    <>
      <div className="wp-travel-section-header">
        <h2 className="wp-travel-section-header-title">
          {__("Maps Settings", "wp-travel")}
        </h2>
        <p className="wp-travel-section-header-description">
          {__("More maps settings according to your choice.", "wp-travel")}
        </p>
      </div>
      <div className='wp-travel-section-content'>
        <PanelRow>
          <label>{__("Select Map", "wp-travel")}</label>
          <div className="wp-travel-field-value">
            <div id="select-map" className="wp-travel-select-wrapper">
              <Select
                theme={(theme) => ({
                  ...theme,
                  borderRadius: ".5rem",
                  colors: {
                    ...theme.colors,
                    primary25: "rgb(236 248 244)",
                    primary50: "rgb(204, 204, 204)",
                    primary: "rgb(7 152 18)"
                  }
                })}
                options={mapOptions}
                value={
                  "undefined" != typeof selectedMap[0] &&
                    "undefined" != typeof selectedMap[0].label
                    ? selectedMap[0]
                    : []
                }
                onChange={(data) => {
                  if ("" !== data) {
                    updateSettings({
                      ...allData,
                      wp_travel_map: data.value,
                    });
                  }
                }}
              />
            </div>
            <p className="description">
              {__(
                "Choose your map provider to display map in site.",
                "wp-travel"
              )}
            </p>
          </div>
        </PanelRow>

        {"google-map" == wp_travel_map && (
          <>
            <PanelRow>
              <label>
                {__("API Key", "wp-travel")}
                <Tooltip
                  text={__(
                    "If you don't have API Key, you can use Map by using Lat/Lng or Location from location tab under trip edit page.",
                    "wp-travel"
                  )}
                >
                  <span>
                    <i class="fa fa-info-circle" aria-hidden="true"></i>
                  </span>
                </Tooltip>
              </label>
              <div className="wp-travel-field-value">
                <TextControl
                    ref={thisRef}
                    id="api-key"
                  // help={__( 'To get your Google map API keys click here', 'wp-travel' )}
                  value={google_map_api_key}
                  onChange={(value) => {
                    updateSettings({
                      ...allData,
                      google_map_api_key: value,
                    });
                  }}
                />
                <p className="description">
                  {__("To get your Google map V3 API keys ", "wp-travel")}{" "}
                  <a
                    href="https://developers.google.com/maps/documentation/javascript/get-api-key"
                    target="_blank"
                  >
                    {__("click here ", "wp-travel")}
                  </a>
                </p>
              </div>
            </PanelRow>
            <PanelRow>
              <label>{__("Zoom Level", "wp-travel")}</label>
              <div className="wp-travel-field-value">
                <TextControl
                  // help={__( 'Set default zoom level of map.', 'wp-travel' )}
                  type="number"
                  value={google_map_zoom_level}
                  onChange={(value) => {
                    updateSettings({
                      ...allData,
                      google_map_zoom_level: value,
                    });
                  }}
                />
                <p className="description">
                  {__("Set default zoom level of map.", "wp-travel")}
                </p>
              </div>
            </PanelRow>
          </>
        )}
        {applyFilters("wp_travel_settings_after_maps_fields", [])}
        {applyFilters("wp_travel_settings_after_maps_upsell", [])}
      </div>
    </>
  );
};

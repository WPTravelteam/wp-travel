import { applyFilters } from "@wordpress/hooks";
import { useSelect, dispatch } from "@wordpress/data";
import { _n, __ } from "@wordpress/i18n";
import { PanelRow, TextControl } from "@wordpress/components";
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

  return (
    <>
      <div className="wp-travel-section-header">
        <h2 className="wp-travel-section-header-title">
          {_wp_travel.setting_strings.maps.maps_settings}
        </h2>
        <p className="wp-travel-section-header-description">
          {__("More maps settings according to your choice.", "wp-travel")}
        </p>
      </div>
      <div className='wp-travel-section-content'>
        <PanelRow>
          <label>{_wp_travel.setting_strings.maps.select_map}</label>
          <div id="wp-travel-select-map" className="wp-travel-field-value">
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
              {_wp_travel.setting_strings.maps.select_map_note}
            </p>
          </div>
        </PanelRow>

        {"google-map" == wp_travel_map && (
          <>
            <PanelRow>
              <label>
                {_wp_travel.setting_strings.maps.api_key}
                <Tooltip
                  text={_wp_travel.setting_strings.maps.api_key_tooltip}
                >
                  <span>
                    <i className="fa fa-info-circle" aria-hidden="true"></i>
                  </span>
                </Tooltip>
              </label>
              <div id="wp-travel-api-key" className="wp-travel-field-value">
                <TextControl
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
                  {_wp_travel.setting_strings.maps.api_key_note}
                  <a
                    href="https://developers.google.com/maps/documentation/javascript/get-api-key"
                    target="_blank"
                  >
                    {_wp_travel.setting_strings.maps.api_key_link_label}
                  </a>
                </p>
              </div>
            </PanelRow>
            <PanelRow>
              <label>{_wp_travel.setting_strings.maps.zoom_level}</label>
              <div id="wp-travel-zoom-level" className="wp-travel-field-value">
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
                  {_wp_travel.setting_strings.maps.zoom_level_note}
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

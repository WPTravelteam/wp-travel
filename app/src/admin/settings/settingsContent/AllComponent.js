import SettingsCurrency from "./general/SettingsCurrency";
import SettingsMaps from "./general/SettingsMaps";
import SettingsPages from "./general/SettingsPages";

export default () => {
    return <div id="all-component">
        <div id="tab-component-currency" className="all-tab-component">
            <SettingsCurrency />
        </div>
        <div id="tab-component-maps" className="all-tab-component">
            <SettingsMaps />
        </div>
        <div id="tab-component-pages" className="all-tab-component"> 
        <SettingsPages />
        </div>
    </div>
}

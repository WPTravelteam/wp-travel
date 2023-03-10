import { useState, useEffect } from '@wordpress/element'
import { _n, __ } from "@wordpress/i18n";

import Select from "react-select";
import { defaultTheme } from "react-select";
import options from './Search/options'

const { colors } = defaultTheme;

const selectStyles = {
  control: (provided) => ({ ...provided, margin: 8 }),
  menu: () => ({ boxShadow: "inset 0 1px 0 rgba(0, 0, 0, 0.1)" }),
  menuPortal: () => ({ zIndex: "1000", width: "240px", position: "absolute", top: "214px", backgroundColor: "white", left: "17px", boxShadow: "inset 0 1px 0 rgba(0, 0, 0, 0.1)" })
};

export default (props) => {
  const [isOpen, setIsOpen] = useState(false);
  const [value, setValue] = useState(undefined);

  const toggleOpen = () => {
    setIsOpen((prevIsOpen) => !prevIsOpen);
  };

  const onSelectChange = (selectedOption) => {
    toggleOpen();
    props.handleTabClick(selectedOption.tab)
    setTimeout(() => {
      let offsetValue = 30

      window.innerWidth < 1024 &&
        window.innerWidth < 768
        ? window.innerWidth < 576
          ? offsetValue = 130
          : offsetValue = 150
        : offsetValue = 50

      let element = document.getElementById("wp-travel-" + selectedOption.value)
      if (element != undefined) {
        let offsetPosition = element.getBoundingClientRect().top + window.scrollY - offsetValue
        window.scrollTo({ top: offsetPosition, behavior: "smooth" })
      }
    }, 50)
  };

  return (
    <Dropdown
      isOpen={isOpen}
      className="wp-travel-quick-search-container"
      onClose={toggleOpen}
      target={
        <button className="wp-travel-quick-search" onClick={toggleOpen}>
          <i
            className="fa fa-search wp-travel-search-icon"
            aria-hidden="true"
          ></i>
          <span
            id="wp-travel-quick-search-text"
          >
            {__("Quick Search...", "wp-travel")}
          </span>
        </button>
      }
    >
      <div className="wp-travel-modal-wrapper">
        <div className="wp-travel-modal">
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
            className="wp-travel-searchbox-container"
            autoFocus
            backspaceRemovesValue={false}
            components={{ DropdownIndicator, IndicatorSeparator: null }}
            controlShouldRenderValue={false}
            hideSelectedOptions={false}
            isClearable={false}
            onChange={e => onSelectChange(e)}
            options={options}
            placeholder="Search..."
            styles={selectStyles}
            tabSelectsValue={false}
            value={value}
          />
        </div>
      </div>
    </Dropdown>
  );
};

const Menu = (props) => {
  const shadow = "hsla(218, 50%, 10%, 0.1)";
  return (
    <div
      style={{
        backgroundColor: "white",
        borderRadius: '.5rem',
        boxShadow: `0 0 0 1px ${shadow}, 0 4px 11px ${shadow}`,
        marginTop: 8,
        position: "fixed",
        top: '20%',
        left: '20%',
        right: '20%',
        width: '60%',
        maxWidth: '800px',
        zIndex: 1000
      }}
      {...props}
    />
  );
};
const Backdrop = (props) => (
  <div
    style={{
      bottom: 0,
      left: 0,
      top: 0,
      right: 0,
      position: "fixed",
      backgroundColor: 'rgba(0, 0, 0, 0.7)',
      zIndex: 200
    }}
    {...props}
  />
);
const Dropdown = ({ children, isOpen, target, onClose }) => (
  <div style={{ position: "relative" }}>
    {target}
    {isOpen ? <Menu>{children}</Menu> : null}
    {isOpen ? <Backdrop onClick={onClose} /> : null}
  </div>
);
const Svg = (p) => (
  <svg
    width="24"
    height="24"
    viewBox="0 0 24 24"
    focusable="false"
    role="presentation"
    {...p}
  />
);
const DropdownIndicator = () => (
  <div style={{ color: colors.neutral20, height: 24, width: 32 }}>
    <Svg>
      <path
        d="M16.436 15.085l3.94 4.01a1 1 0 0 1-1.425 1.402l-3.938-4.006a7.5 7.5 0 1 1 1.423-1.406zM10.5 16a5.5 5.5 0 1 0 0-11 5.5 5.5 0 0 0 0 11z"
        fill="currentColor"
        fillRule="evenodd"
      />
    </Svg>
  </div>
);

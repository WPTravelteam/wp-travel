import { useState, useEffect, createPortal } from '@wordpress/element'
import { _n, __ } from "@wordpress/i18n";

import Select from "../../UI/Select";
import { defaultTheme } from "react-select";
import options from './options'

const { colors } = defaultTheme;

const selectStyles = {
  control: (provided) => ({ ...provided, margin: 8 }),
  menu: () => ({ boxShadow: "inset 0 1px 0 rgba(0, 0, 0, 0.1)" }),
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
    </Dropdown>
  );
};

const Menu = (props) => {
  return (
    <div
      className="wp-travel-search-modal-menu"
      {...props}
    />
  );
};
const Backdrop = (props) => (
  <div
    className='wp-travel-search-modal-backdrop'
    {...props}
  />
);
const Dropdown = ({ children, isOpen, target, onClose }) => (
  <div style={{ position: "relative" }}>
    {target}
    {isOpen &&
      createPortal(
          <div id="wp-travel-search-modal-wrapper">
            <Menu>{children}</Menu>
            <Backdrop onClick={onClose} />
          </div>
        , document.getElementById('wpwrap'))
    }
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

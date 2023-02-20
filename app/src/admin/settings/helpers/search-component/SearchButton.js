import { forwardRef, useState } from '@wordpress/element'
import { _n, __ } from "@wordpress/i18n";

export default forwardRef((props, ref) => {
  const [modalOpened, setModalOpened] = useState(false);

  const modalToggle = () => {
    setModalOpened(!modalOpened);
  }

  const coverClass = modalOpened ? 'wp-travel-modal-cover wp-travel-modal-cover-active' : 'wp-travel-modal-cover';
  const containerClass = modalOpened ? 'wp-travel-modal-container wp-travel-modal-container-active' : 'wp-travel-modal-container';

  return (
    <>
      <button ref={ref} className="wp-travel-quick-search" onClick={modalToggle}>
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
      {modalOpened && <Modal coverClass={coverClass} containerClass={containerClass} modalToggle={modalToggle} />}
    </>
  )
})

const Modal = ({ modalToggle, containerClass, coverClass }) => {
  return (
    <div className="wp-travel-modal">
      <div className={containerClass}>
        <div style={{color: 'black', zIndex: '1060'}}>
          <i className="fa fa-close"></i>
        </div>
      </div>
      <div className={coverClass} onClick={modalToggle}></div>
    </div>
  );
}


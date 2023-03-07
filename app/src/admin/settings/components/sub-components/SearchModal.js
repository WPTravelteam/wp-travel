import { forwardRef, useState } from '@wordpress/element'
import { _n, __ } from "@wordpress/i18n";
import Transition from '../UI/Transition';

export default forwardRef((props, ref, children) => {
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
      {
        modalOpened &&
        <Modal children={children} coverClass={coverClass} containerClass={containerClass} modalToggle={modalToggle} />
      }
    </>
  )
})

const Modal = ({ modalToggle, containerClass, coverClass, children }) => {
  return (
    <div className="wp-travel-modal">
      <Transition duration={300} translateX={0} translateY={25} className="wp-travel-modal-wrapper">
        <div className={containerClass}>
          <div style={{ color: 'black', zIndex: '1060' }} onClick={modalToggle}>
            <i style={{ color: "black" }} className="fa fa-times"></i>
          </div>
          {children}
        </div>
      </Transition>
      <div className={coverClass} onClick={modalToggle}></div>
    </div>
  );
}


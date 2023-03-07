import { useState, useEffect } from '@wordpress/element'

const Tooltip = ({ children, text, className }) => {
    const [showTooltip, setShowTooltip] = useState(false);

    const handleHover = () => {
        setShowTooltip(true)
    }

    const handleHoverOut = () => {
        setShowTooltip(false)
    }

    return (
        <div id='wp-travel-tooltip-container'>
            <>
                <div onMouseOver={handleHover} onMouseOut={handleHoverOut} className={className}>{children}</div>
                <Popover mounted={showTooltip} onMouseOver={handleHover} onMouseOut={handleHoverOut} text={text} />
            </>
        </div>
    )
}

export default Tooltip

const Popover = (props) => {
    const [show, setShow] = useState(false);
    const [style, setStyle] = useState({
        opacity: 0,
        transform: `translate(0, 20px)`,
        transition: "all 100ms ease-in-out 0.3s"
    });

    useEffect(() => {
        if (!props.mounted) {
            unMountStyle();
        } else {
            setShow(true);
            setTimeout(mountStyle, 10);
        }
    }, [props.mounted]);

    const unMountStyle = () => {
        setStyle({
            opacity: 0,
            transform: `translate(0, 20px)`,
            transition: "all 50ms ease-in-out 50ms"
        });
    };

    const mountStyle = () => {
        setStyle({
            opacity: 1,
            zIndex: 1000,
            transform: `translate(0)`,
            transition: "all 100ms ease-in-out 0.1s",
            transitionDelay: `0.5s`
        });
    };

    const transitionEnd = () => {
        if (!props.mounted) {
            setShow(false);
        }
    };

    return (
        show && (
            <div
                id="wp-travel-tooltip"
                mounted={show}
                style={style}
                onTransitionEnd={transitionEnd}
                onMouseOver={props.onMouseOver}
                onMouseOut={props.onMouseOut}
            >
                {props.text}
            </div>
        )
    )
}
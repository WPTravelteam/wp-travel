import { useState, useEffect } from '@wordpress/element'

const Transition = ({
    children,
    className,
    duration = 200,
    translateX = 0,
    translateY = 0,
    delay = 0,
    zIndex = 0,
    property = "all"
}) => {
    const [styles, setStyles] = useState({
        opacity: 0,
        transform: `translate(${translateX}, ${translateY}px)`,
        transition: `${property} ${duration}ms ease-in-out`,
        transitionDelay: `${delay}s`
    });

    useEffect(() => {
        setStyles({
            opacity: 1,
            zIndex: zIndex,
            transform: `translate(${0}, ${0})`,
            transition: `${property} ${duration}ms ease-in-out ${delay}s`,
        })
    }, [])

    return (
        <div className={className} style={styles}>{children}</div>
    )
}

export default Transition;
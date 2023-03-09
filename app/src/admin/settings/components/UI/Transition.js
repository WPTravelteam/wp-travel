import { useState, useEffect } from '@wordpress/element'

const Transition = ({
    style, 
    children,
    className,
    duration = 200,
    translateX = 0,
    translateY = 0,
    delay = 0,
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
            transform: `translate(${0}, ${0})`,
            transition: `${property} ${duration}ms ease-in-out ${delay}s`,
        })
    }, [])

    return (
        <div className={className} style={{...style, styles}}>{children}</div>
    )
}

export default Transition
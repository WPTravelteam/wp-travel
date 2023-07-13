import React, { useEffect, useRef, useState } from 'react';
import './ProgressBar.css';

function ProgressBary(props) {
  const [width, setWidth] = useState(0);
  const barRef = useRef(null);
  const prevWidthRef = useRef(0);  // Store a reference to the previous width value

  useEffect(() => {
    const endWidth = props.value / props.max * 100;
    const animationStartTime = performance.now();

    function updateWidth(timestamp) {
      const progress = (timestamp - animationStartTime) / 1000;
      const startWidth = prevWidthRef.current;  // Use previous width value as startWidth
      const newWidth = startWidth + (endWidth - startWidth) * progress;
      setWidth(Math.min(newWidth, 100));
      if (progress < 1) {
        requestAnimationFrame(updateWidth);
      }
    }

    requestAnimationFrame(updateWidth);

  }, [props.value, props.max]);

  useEffect(() => {
    const bar = barRef.current;
    bar.style.width = `${width.toFixed(2)}%`;
    prevWidthRef.current = width;  // Update previous width value
  }, [width]);

  return (
    <div className="progress-bar">
      <div className="bar-container">
        <div className="bar" ref={barRef}></div>
      </div>
      <div className="progress-text">
        <span className="progress-status">{props.statusText}</span>
        <br />
        <span className="progress-percentage">{width.toFixed(2)}%</span>
      </div>
    </div>
  );
}

export default ProgressBary;
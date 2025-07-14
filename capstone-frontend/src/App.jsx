import React, { useEffect, useState } from 'react';
import echo from './echo';
import './App.css';

// Get readable pH scale label
const getPHScale = (value) => {
  const ph = parseFloat(value);
  if (isNaN(ph)) return 'Invalid';

  // if (ph < 0) return 'Extremely Acidic';
  // if (ph < 3) return 'Strongly Acidic';

  if (ph >= 6.5 && ph <= 7.5) return 'All good! Your Aquarium is Healthy';
  if (ph < 3.5) return 'Water is Too Acidic,this can stress or harm your fish';
  if (ph < 6.5) return 'Water is Acidic,this can stress or harm your fish';
  if (ph > 7.5 && ph < 11) return 'Water is Alkaline, this may affect your fish health.';
  if (ph > 11) return 'Water is Too Alkaline, this may affect your fish health.';
  // if (ph < 7) return 'Acidic';
  // if (ph === 7) return 'Neutral';
  // if (ph <= 11) return 'Alkaline';
  // if (ph <= 14) return 'Strongly Alkaline';
  // return 'Extremely Alkaline';
};

// Get Tailwind class based on pH value
const getPHColorClass = (value) => {
  const ph = parseFloat(value);
  if (isNaN(ph)) return 'text-gray-500';

  // if (ph < 0) return 'text-red-900';
  if (ph < 6.5) return 'text-red-600';
  // if (ph < 7) return 'text-orange-500';
  if (ph >=6.5 && ph <= 7.5) return 'text-green-600';
  if (ph > 7.5) return 'text-red-500';
  // if (ph <= 14) return 'text-indigo-600';
  return 'text-purple-800';
};
const topStatus = (value) => {
  const ph = parseFloat(value);
  if (ph < 6.5) return '❗ ';
  if (ph >=6.5 && ph <= 7.5) return '✅ ';
  if (ph > 7.5) return '❗ ';
}
const getStatus = (value) => {
   const ph = parseFloat(value);
  if (ph < 6.5) return 'pH Too Low';
  if (ph >=6.5 && ph <= 7.5) return 'pH is Normal';
  if (ph > 7.5) return 'pH Too High';
}

const App = () => {
  const [sensorData, setSensorData] = useState(null);

  useEffect(() => {
    const channel = echo.channel('sensor-readings')
      .listen('.new-reading', (e) => {
        setSensorData(e.reading.ph);
      });

    // Cleanup on unmount
    return () => {
      channel.stopListening('.new-reading');
    };
  }, []);

  const label = getPHScale(sensorData);
  const colorClass = getPHColorClass(sensorData);
  const status = getStatus(sensorData);

  const topStats = topStatus(sensorData);

  return (
    <div className='my-wrapper'>
      <div className='header'>
        <h1>pH Monitoring</h1>
      </div>

      {sensorData !== null && sensorData !== undefined ? (
        <div className='content'>
          <div className='stats'>
            {/* `${colorClass}` */}
            <span>{topStats }</span>
            <span>{status}</span>
          </div>
          <p className='text-lg ph'>Current pH: {sensorData}</p>
          <p className={`text-sm font-medium ${colorClass}`}>{label}</p>
        </div>
      ) : (
        <p className='no-dta'>No Reading Yet</p>
      )}
    </div>
  );
};

export default App;

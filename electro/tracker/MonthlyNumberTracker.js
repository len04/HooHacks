// MonthlyNumberTracker.js
import React, { useState, useEffect } from 'react';
import axios from 'axios';

const MonthlyNumberTracker = () => {
  const [monthlyNumbers, setMonthlyNumbers] = useState([]);
  const [newNumber, setNewNumber] = useState('');
  const [selectedMonth, setSelectedMonth] = useState('');

  // Fetch monthly numbers from the backend
  useEffect(() => {
    axios.get('http://localhost:8000/monthly_numbers/')
      .then(response => {
        setMonthlyNumbers(response.data);
      })
      .catch(error => console.error('There was an error fetching the monthly numbers:', error));
  }, []);

  // Handle form submission
  const handleSubmit = (e) => {
    e.preventDefault();
    axios.post('http://localhost:8000/monthly_numbers/', {
      user: 1, // Assuming user ID is 1 for demonstration purposes
      month: selectedMonth,
      number: newNumber
    })
    .then(response => {
      setMonthlyNumbers([...monthlyNumbers, response.data]);
      setNewNumber('');
      setSelectedMonth('');
    })
    .catch(error => console.error('There was an error posting the monthly number:', error));
  };

  return (
    <div>
      <h2>Monthly Number Tracker</h2>
      <form onSubmit={handleSubmit}>
        <label>
          Month:
          <input
            type="month"
            value={selectedMonth}
            onChange={(e) => setSelectedMonth(e.target.value)}
            required
          />
        </label>
        <label>
          Number:
          <input
            type="number"
            value={newNumber}
            onChange={(e) => setNewNumber(e.target.value)}
            required
          />
        </label>
        <button type="submit">Submit</button>
      </form>
      <ul>
        {monthlyNumbers.map((entry) => (
          <li key={entry.id}>{entry.month}: {entry.number}</li>
        ))}
      </ul>
    </div>
  );
};

export default MonthlyNumberTracker;
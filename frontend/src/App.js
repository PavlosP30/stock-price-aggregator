import React, { useEffect, useState } from 'react';
import './App.css';

function App() {
    const [stocks, setStocks] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const fetchStockData = async () => {
            try {
                const response = await fetch('http://localhost/api/reports/stocks');
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                if (data.success) {
                    setStocks(data.data);
                }
            } catch (error) {
                console.error('Error fetching stock data:', error);
            } finally {
                setLoading(false);
            }
        };

        fetchStockData();

        // Fetch data every 1 minute
        const interval = setInterval(fetchStockData, 60000);
        return () => clearInterval(interval);
    }, []);

    if (loading) {
        return <div className="loading">Loading stock data...</div>;
    }

    return (
        <div className="app">
            <h1>Stock Dashboard</h1>
            <table className="stock-table">
                <thead>
                <tr>
                    <th>Symbol</th>
                    <th>Name</th>
                    <th>Latest Close</th>
                    <th>Latest Volume</th>
                    <th>Latest Change (%)</th>
                </tr>
                </thead>
                <tbody>
                {stocks.map((stock) => {
                    const latestPrice = stock.time_series[0] || {}; // Get the latest time series entry or an empty object
                    const close = latestPrice.close ?? 'N/A';
                    const volume = latestPrice.volume ?? 'N/A';
                    const percentageChange = latestPrice.percentage_change ?? 'N/A';

                    return (
                        <tr key={stock.symbol}>
                            <td>{stock.symbol}</td>
                            <td>{stock.name}</td>
                            <td>{close}</td>
                            <td>{volume}</td>
                            <td>
                  <span
                      className={
                          percentageChange > 0
                              ? 'change positive'
                              : percentageChange < 0
                                  ? 'change negative'
                                  : 'change neutral'
                      }
                  >
                    {percentageChange > 0 ? '↑' : percentageChange < 0 ? '↓' : ''}
                      {percentageChange}%
                  </span>
                            </td>
                        </tr>
                    );
                })}
                </tbody>
            </table>
        </div>
    );
}

export default App;

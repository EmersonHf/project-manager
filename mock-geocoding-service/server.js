const express = require('express');
const data = require('./geocode_data.json'); // The JSON file you created
const app = express();

// GET /geocode?address=some+address
app.get('/geocode', (req, res) => {
    const address = req.query.address || '';
    // Return lat/lng if address matches, else a default or null
    if (data[address]) {
        res.json({
            lat: data[address].lat,
            lng: data[address].lng
        });
    } else {
        // If address not in our file, return null or a dummy location
        res.json({ lat: 0, lng: 0 });
    }
});

const PORT = 3001;
app.listen(PORT, () => {
    console.log(`Mock geocoding service running on port ${PORT}`);
});

const fs = require('fs');
const path = require('path');

const logFile = path.join(__dirname, '../logs/app.log');

function log(message, level = 'INFO') {
    const timestamp = new Date().toISOString();
    const logMessage = `[${timestamp}] [${level}] ${message}\n`;
    
    fs.appendFile(logFile, logMessage, (err) => {
        if (err) {
            console.error('Error writing to log file:', err);
        }
    });

    // Also log to console in development environment
    if (process.env.NODE_ENV !== 'production') {
        console.log(logMessage);
    }
}

module.exports = {
    info: (message) => log(message, 'INFO'),
    error: (message) => log(message, 'ERROR'),
    warn: (message) => log(message, 'WARN'),
    debug: (message) => log(message, 'DEBUG')
};
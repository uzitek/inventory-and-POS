const nodemailer = require('nodemailer');

const transporter = nodemailer.createTransport({
    host: 'smtp.example.com',
    port: 587,
    secure: false, // Use TLS
    auth: {
        user: 'your-email@example.com',
        pass: 'your-password'
    }
});

function sendEmail(to, subject, text, html) {
    return new Promise((resolve, reject) => {
        const mailOptions = {
            from: '"Your Company Name" <noreply@example.com>',
            to: to,
            subject: subject,
            text: text,
            html: html
        };

        transporter.sendMail(mailOptions, (error, info) => {
            if (error) {
                reject(error);
            } else {
                resolve(info);
            }
        });
    });
}

module.exports = {
    sendEmail
};
module.exports = {
    isAuthenticated: (req, res, next) => {
        if (req.session.userId) {
            return next();
        }
        res.redirect('/auth/login');
    },
    
    isAdmin: (req, res, next) => {
        if (req.session.userId && req.session.userRole === 1) { // Assuming 1 is the admin role
            return next();
        }
        res.status(403).send('Access denied');
    }
};
const express = require('express');
const router = express.Router();
const orderController = require('../controllers/orderController');

router.get('/', orderController.getAllOrders);
router.get('/add', orderController.getAddOrder);
router.post('/add', orderController.postAddOrder);
router.get('/view/:id', orderController.getViewOrder);
router.get('/invoice/:id', orderController.generateInvoice);

module.exports = router;
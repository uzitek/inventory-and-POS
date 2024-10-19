const express = require('express');
const router = express.Router();
const productController = require('../controllers/productController');

router.get('/', productController.getAllProducts);
router.get('/add', productController.getAddProduct);
router.post('/add', productController.postAddProduct);
router.get('/edit/:id', productController.getEditProduct);
router.post('/edit/:id', productController.postEditProduct);
router.post('/delete/:id', productController.deleteProduct);

module.exports = router;
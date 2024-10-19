const db = require('../config/database');
const multer = require('multer');
const path = require('path');

const storage = multer.diskStorage({
  destination: (req, file, cb) => {
    cb(null, 'public/uploads/');
  },
  filename: (req, file, cb) => {
    cb(null, Date.now() + path.extname(file.originalname));
  }
});

const upload = multer({ storage: storage });

exports.getAllProducts = async (req, res) => {
  try {
    const [products] = await db.query('SELECT * FROM products');
    res.render('products/index', { products });
  } catch (error) {
    console.error(error);
    res.status(500).send('Server error');
  }
};

exports.getAddProduct = (req, res) => {
  res.render('products/add');
};

exports.postAddProduct = [
  upload.single('image'),
  async (req, res) => {
    const { name, description, category_id, price, quantity } = req.body;
    const image_url = req.file ? `/uploads/${req.file.filename}` : null;

    try {
      await db.query(
        'INSERT INTO products (name, description, category_id, price, quantity, image_url) VALUES (?, ?, ?, ?, ?, ?)',
        [name, description, category_id, price, quantity, image_url]
      );
      res.redirect('/products');
    } catch (error) {
      console.error(error);
      res.status(500).send('Server error');
    }
  }
];

exports.getEditProduct = async (req, res) => {
  const productId = req.params.id;
  try {
    const [products] = await db.query('SELECT * FROM products WHERE id = ?', [productId]);
    if (products.length > 0) {
      res.render('products/edit', { product: products[0] });
    } else {
      res.status(404).send('Product not found');
    }
  } catch (error) {
    console.error(error);
    res.status(500).send('Server error');
  }
};

exports.postEditProduct = [
  upload.single('image'),
  async (req, res) => {
    const productId = req.params.id;
    const { name, description, category_id, price, quantity } = req.body;
    const image_url = req.file ? `/uploads/${req.file.filename}` : req.body.existing_image;

    try {
      await db.query(
        'UPDATE products SET name = ?, description = ?, category_id = ?, price = ?, quantity = ?, image_url = ? WHERE id = ?',
        [name, description, category_id, price, quantity, image_url, productId]
      );
      res.redirect('/products');
    } catch (error) {
      console.error(error);
      res.status(500).send('Server error');
    }
  }
];

exports.deleteProduct = async (req, res) => {
  const productId = req.params.id;
  try {
    await db.query('DELETE FROM products WHERE id = ?', [productId]);
    res.redirect('/products');
  } catch (error) {
    console.error(error);
    res.status(500).send('Server error');
  }
};
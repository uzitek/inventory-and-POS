const db = require('../config/database');
const PDFDocument = require('pdfkit');
const fs = require('fs');

exports.getAllOrders = async (req, res) => {
  try {
    const [orders] = await db.query('SELECT * FROM orders ORDER BY created_at DESC');
    res.render('orders/index', { orders });
  } catch (error) {
    console.error(error);
    res.status(500).send('Server error');
  }
};

exports.getAddOrder = async (req, res) => {
  try {
    const [products] = await db.query('SELECT * FROM products');
    res.render('orders/add', { products });
  } catch (error) {
    console.error(error);
    res.status(500).send('Server error');
  }
};

exports.postAddOrder = async (req, res) => {
  const { items, total_amount } = req.body;
  const userId = req.session.userId;

  try {
    const [result] = await db.query('INSERT INTO orders (user_id, total_amount) VALUES (?, ?)', [userId, total_amount]);
    const orderId = result.insertId;

    for (const item of items) {
      await db.query('INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)',
        [orderId, item.product_id, item.quantity, item.price]);
      await db.query('UPDATE products SET quantity = quantity - ? WHERE id = ?',
        [item.quantity, item.product_id]);
    }

    res.json({ success: true, orderId });
  } catch (error) {
    console.error(error);
    res.status(500).json({ success: false, message: 'Server error' });
  }
};

exports.getViewOrder = async (req, res) => {
  const orderId = req.params.id;
  try {
    const [orders] = await db.query('SELECT * FROM orders WHERE id = ?', [orderId]);
    const [orderItems] = await db.query('SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?', [orderId]);
    
    if (orders.length > 0) {
      res.render('orders/view', { order: orders[0], orderItems });
    } else {
      res.status(404).send('Order not found');
    }
  } catch (error) {
    console.error(error);
    res.status(500).send('Server error');
  }
};

exports.generateInvoice = async (req, res) => {
  const orderId = req.params.id;
  try {
    const [orders] = await db.query('SELECT * FROM orders WHERE id = ?', [orderId]);
    const [orderItems] = await db.query('SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?', [orderId]);
    
    if (orders.length > 0) {
      const order = orders[0];
      const doc = new PDFDocument();
      const filename = `invoice-${orderId}.pdf`;
      
      res.setHeader('Content-disposition', `attachment; filename="${filename}"`);
      res.setHeader('Content-type', 'application/pdf');
      
      doc.pipe(res);
      
      // Add content to PDF
      doc.fontSize(18).text('Invoice', { align: 'center' });
      doc.moveDown();
      doc.fontSize(12).text(`Order ID: ${order.id}`);
      doc.text(`Date: ${order.created_at}`);
      doc.moveDown();
      
      orderItems.forEach(item => {
        doc.text(`${item.name} - Quantity: ${item.quantity} - Price: $${item.price}`);
      });
      
      doc.moveDown();
      doc.fontSize(14).text(`Total Amount: $${order.total_amount}`, { align: 'right' });
      
      doc.end();
    } else {
      res.status(404).send('Order not found');
    }
  } catch (error) {
    console.error(error);
    res.status(500).send('Server error');
  }
};
const db = require('../config/database');

exports.getDashboard = async (req, res) => {
  try {
    const [totalProducts] = await db.query('SELECT COUNT(*) as count FROM products');
    const [lowStockProducts] = await db.query('SELECT COUNT(*) as count FROM products WHERE quantity <= 10');
    const [recentOrders] = await db.query('SELECT * FROM orders ORDER BY created_at DESC LIMIT 5');
    
    res.render('dashboard/index', {
      totalProducts: totalProducts[0].count,
      lowStockProducts: lowStockProducts[0].count,
      recentOrders: recentOrders
    });
  } catch (error) {
    console.error(error);
    res.status(500).send('Server error');
  }
};
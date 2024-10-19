const db = require('../config/database');
const ExcelJS = require('exceljs');

exports.getReportOptions = (req, res) => {
  res.render('reports/options');
};

exports.generateReport = async (req, res) => {
  const { reportType, startDate, endDate, format } = req.body;

  try {
    let data;
    let headers;

    switch (reportType) {
      case 'inventory':
        [data] = await db.query('SELECT * FROM products');
        headers = ['ID', 'Name', 'Description', 'Category', 'Price', 'Quantity'];
        break;
      case 'sales':
        [data] = await db.query('SELECT * FROM orders WHERE created_at BETWEEN ? AND ?', [startDate, endDate]);
        headers = ['Order ID', 'User ID', 'Total Amount', 'Created At'];
        break;
      default:
        return res.status(400).send('Invalid report type');
    }

    if (format === 'excel') {
      const workbook = new ExcelJS.Workbook();
      const worksheet = workbook.addWorksheet('Report');
      
      worksheet.addRow(headers);
      data.forEach(row => worksheet.addRow(Object.values(row)));

      res.setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
      res.setHeader('Content-Disposition', `attachment; filename=${reportType}-report.xlsx`);

      await workbook.xlsx.write(res);
      res.end();
    } else {
      res.render(`reports/${reportType}`, { data, startDate, endDate });
    }
  } catch (error) {
    console.error(error);
    res.status(500).send('Server error');
  }
};
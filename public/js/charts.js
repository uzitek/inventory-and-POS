function createLineChart(ctx, labels, data) {
  const width = ctx.canvas.width;
  const height = ctx.canvas.height;
  const padding = 40;

  // Clear the canvas
  ctx.clearRect(0, 0, width, height);

  // Draw axes
  ctx.beginPath();
  ctx.moveTo(padding, padding);
  ctx.lineTo(padding, height - padding);
  ctx.lineTo(width - padding, height - padding);
  ctx.stroke();

  // Calculate scales
  const xScale = (width - 2 * padding) / (labels.length - 1);
  const yScale = (height - 2 * padding) / Math.max(...data);

  // Draw data points and lines
  ctx.beginPath();
  ctx.moveTo(padding, height - padding - data[0] * yScale);
  for (let i = 0; i < data.length; i++) {
    const x = padding + i * xScale;
    const y = height - padding - data[i] * yScale;
    ctx.lineTo(x, y);
    ctx.arc(x, y, 3, 0, 2 * Math.PI);
  }
  ctx.stroke();

  // Draw labels
  ctx.font = '12px Arial';
  ctx.textAlign = 'center';
  for (let i = 0; i < labels.length; i++) {
    const x = padding + i * xScale;
    ctx.fillText(labels[i], x, height - padding + 20);
  }
}
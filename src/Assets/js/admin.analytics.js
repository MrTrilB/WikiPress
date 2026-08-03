document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.wikipress-analytics-table tbody tr').forEach((row, index) => {
    row.style.animationDelay = `${index * 35}ms`;
    row.classList.add('wikipress-analytics-row');
  });
});

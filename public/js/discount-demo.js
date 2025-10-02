document.addEventListener('DOMContentLoaded', () => {
  const type = document.getElementById('typeSelect');
  const label = document.getElementById('valueLabel');
  if (!type || !label) return;

  const update = () => {
    label.textContent = type.value === 'fixed' ? 'Value (₹)' : 'Value (%)';
  };

  type.addEventListener('change', update);
  update();
});

const money = (value) => Number(value || 0).toFixed(2);

async function api(path, options = {}) {
  const response = await fetch('/api' + path, {
    headers: { 'Content-Type': 'application/json' },
    ...options,
  });
  const data = await response.json().catch(() => ({}));
  if (!response.ok) {
    throw new Error(data.message || 'Request failed');
  }
  return data;
}

function showPage(name) {
  document.querySelectorAll('.page').forEach((page) => page.classList.add('hidden'));
  document.getElementById('page-' + name).classList.remove('hidden');
  document.querySelectorAll('.nav-btn').forEach((btn) => {
    btn.classList.toggle('active', btn.dataset.page === name);
  });
}

async function refreshStatus() {
  const el = document.getElementById('apiStatus');
  try {
    const health = await api('/health');
    el.textContent = 'Local API online';
    el.classList.remove('offline');
    el.title = health.app || '';
  } catch (error) {
    el.textContent = 'API offline';
    el.classList.add('offline');
  }
}

async function fillSelect(id, rows, labelKey = 'name') {
  const select = document.getElementById(id);
  select.innerHTML = rows.map((row) => `<option value="${row.id}">${row[labelKey]}</option>`).join('');
}

async function loadDashboard() {
  const data = await api('/dashboard');
  document.getElementById('dashIncome').textContent = money(data.income);
  document.getElementById('dashExpense').textContent = money(data.expense);
  document.getElementById('dashBalance').textContent = money(data.balance);
}

async function loadExpenses() {
  const rows = await api('/expenses');
  document.getElementById('expenseRows').innerHTML = rows.map((row) => `
    <tr>
      <td>${row.date}</td>
      <td>${row.category_name}</td>
      <td>${money(row.amount)}</td>
      <td>${money(row.vat_amount)}</td>
      <td>${row.supplier_name || ''}</td>
      <td>${row.proof_type}</td>
      <td>${row.description || ''}</td>
    </tr>
  `).join('') || '<tr><td colspan="7">No expenses yet</td></tr>';
}

async function loadIncome() {
  const rows = await api('/incomes');
  document.getElementById('incomeRows').innerHTML = rows.map((row) => `
    <tr>
      <td>${row.date}</td>
      <td>${row.category_name}</td>
      <td>${money(row.amount)}</td>
      <td>${row.description || ''}</td>
    </tr>
  `).join('') || '<tr><td colspan="4">No income yet</td></tr>';
}

async function loadReport() {
  const data = await api('/report');
  document.getElementById('reportRows').innerHTML = data.by_category.map((row) => `
    <tr><td>${row.name}</td><td>${money(row.total)}</td></tr>
  `).join('');
}

async function bootFormOptions() {
  const expenseCats = await api('/categories?type=expense');
  const incomeCats = await api('/categories?type=income');
  const suppliers = await api('/suppliers');
  fillSelect('expenseCategory', expenseCats);
  fillSelect('incomeCategory', incomeCats);
  const supplierSelect = document.getElementById('expenseSupplier');
  supplierSelect.innerHTML = '<option value="">None</option>' + suppliers.map((row) => `<option value="${row.id}">${row.name}</option>`).join('');
  const dateInputs = document.querySelectorAll('input[type="date"]');
  dateInputs.forEach((input) => {
    if (!input.value) input.value = new Date().toISOString().slice(0, 10);
  });
}

document.querySelectorAll('.nav-btn').forEach((btn) => {
  btn.addEventListener('click', async () => {
    showPage(btn.dataset.page);
    if (btn.dataset.page === 'dashboard') await loadDashboard();
    if (btn.dataset.page === 'expenses') await loadExpenses();
    if (btn.dataset.page === 'income') await loadIncome();
    if (btn.dataset.page === 'report') await loadReport();
  });
});

const modal = document.getElementById('expenseModal');
document.getElementById('openExpense').addEventListener('click', () => modal.classList.remove('hidden'));
document.getElementById('cancelExpense').addEventListener('click', () => modal.classList.add('hidden'));

document.getElementById('expenseForm').addEventListener('submit', async (event) => {
  event.preventDefault();
  const error = document.getElementById('expenseError');
  error.classList.add('hidden');
  const form = new FormData(event.target);
  const payload = Object.fromEntries(form.entries());
  payload.amount = Number(payload.amount);
  payload.vat_amount = Number(payload.vat_amount || 0);
  payload.category_id = Number(payload.category_id);
  if (payload.supplier_id) payload.supplier_id = Number(payload.supplier_id);
  else payload.supplier_id = null;
  try {
    await api('/expenses', { method: 'POST', body: JSON.stringify(payload) });
    modal.classList.add('hidden');
    event.target.reset();
    await bootFormOptions();
    await loadExpenses();
    await loadDashboard();
    showPage('expenses');
  } catch (err) {
    error.textContent = err.message;
    error.classList.remove('hidden');
  }
});

document.getElementById('incomeForm').addEventListener('submit', async (event) => {
  event.preventDefault();
  const form = new FormData(event.target);
  const payload = Object.fromEntries(form.entries());
  payload.amount = Number(payload.amount);
  payload.category_id = Number(payload.category_id);
  await api('/incomes', { method: 'POST', body: JSON.stringify(payload) });
  event.target.reset();
  await bootFormOptions();
  await loadIncome();
  await loadDashboard();
});

(async function init() {
  await refreshStatus();
  await bootFormOptions();
  await loadDashboard();
  setInterval(refreshStatus, 10000);
})();

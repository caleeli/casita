<script>
import SummaryTable from './lib/SummaryTable.svelte';
import AccountsTable from './lib/AccountsTable.svelte';
import TransactionForm from './lib/TransactionForm.svelte';
import TransactionsTable from './lib/TransactionsTable.svelte';
import dayjs from "dayjs";

const API_BASE = import.meta.env.VITE_API_BASE;
export let fondo = "casita";
$: api_base = `${API_BASE}/${fondo}/`;

  // Transaction form state
  let transaction_id = "";
  let account_id = "";
  let amount = "";
  let type = "-1";
  let memo = "";
  let created_at = dayjs().format("YYYY-MM-DD");

  // Data
  let accounts = [];
  let transactions = [];
  let summary = [];

  // Filters
  let filter_account_id = "";
  let filter_amount = "";
  let filter_created_at = "";
  let filter_memo = "";

  // Loading and error states
  let loading = false;
  let error = "";

  // Fetch helpers
  async function fetchAccounts() {
    try {
      loading = true;
      const response = await fetch(api_base + "accounts");
      accounts = await response.json();
    } catch (e) {
      error = "Error cargando cuentas";
    } finally {
      loading = false;
    }
  }

  async function fetchTransactions() {
    try {
      loading = true;
      let params = [];
      if (filter_account_id) params.push(`account_id=${filter_account_id}`);
      if (filter_amount) params.push(`amount=${filter_amount}`);
      if (filter_created_at) params.push(`created_at=${filter_created_at}`);
      if (filter_memo) params.push(`memo=${filter_memo}`);
      const response = await fetch(
        api_base +
          "transactions?page_size=100" +
          (params.length ? "&" + params.join("&") : "")
      );
      transactions = await response.json();
    } catch (e) {
      error = "Error cargando transacciones";
    } finally {
      loading = false;
    }
  }

  async function fetchSummary() {
    try {
      loading = true;
      const response = await fetch(api_base + "summary");
      summary = await response.json();
    } catch (e) {
      error = "Error cargando resumen";
    } finally {
      loading = false;
    }
  }

  function formatNumber(number) {
    return String(number).replace(/\B(?=(\d{3})+(?!\d))/g, ".");
  }

  async function handleTransactionSubmit() {
    if (transaction_id) {
      await saveEditTransaction();
    } else {
      await registerTransaction();
    }
  }

  async function registerTransaction() {
    if (!account_id) {
      alert("Seleccione una cuenta");
      return;
    }
    try {
      loading = true;
      const response = await fetch(api_base + "transaction", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ account_id, amount, type, memo, created_at }),
      });
      await response.json();
      resetForm();
      await Promise.all([fetchAccounts(), fetchTransactions()]);
    } catch (e) {
      error = "Error registrando transacción";
    } finally {
      loading = false;
    }
  }

  async function removeTransaction(transaction) {
    if (!confirm(`Remover la transacción de ${transaction.memo} por Bs. ${transaction.amount}?`)) return;
    try {
      loading = true;
      await fetch(api_base + "remove_transaction/" + transaction.id, { method: "DELETE" });
      await Promise.all([fetchAccounts(), fetchTransactions()]);
      // Optionally, prefill form for quick edit
      account_id = transaction.account_id;
      amount = transaction.amount;
      type = transaction.type;
      memo = transaction.memo;
      created_at = transaction.created_at;
    } catch (e) {
      error = "Error eliminando transacción";
    } finally {
      loading = false;
    }
  }

  function editTransaction(transaction) {
    transaction_id = transaction.id;
    account_id = transaction.account_id;
    amount = transaction.amount;
    type = transaction.type;
    memo = transaction.memo;
    created_at = transaction.created_at;
  }

  async function saveEditTransaction() {
    if (!transaction_id) {
      alert("Falta ID de la transaccion");
      return;
    }
    if (!account_id) {
      alert("Seleccione una cuenta");
      return;
    }
    try {
      loading = true;
      await fetch(api_base + "update_transaction/" + transaction_id, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ account_id, amount, type, memo, created_at }),
      });
      resetForm();
      await Promise.all([fetchAccounts(), fetchTransactions()]);
    } catch (e) {
      error = "Error actualizando transacción";
    } finally {
      loading = false;
    }
  }

  function resetForm() {
    transaction_id = "";
    account_id = "";
    amount = "";
    type = "-1";
    memo = "";
    created_at = dayjs().format("YYYY-MM-DD");
  }

  function cancelEdit() {
    resetForm();
  }

  function filterTransactions() {
    fetchTransactions();
  }

  // Initial load and when fondo changes
  $: if (fondo) {
    fetchAccounts();
    fetchTransactions();
    fetchSummary();
  }
</script>

<SummaryTable {summary} {formatNumber} />
<AccountsTable accounts={accounts} {formatNumber} />
<TransactionForm
  {accounts}
  bind:transaction_id
  bind:account_id
  bind:amount
  bind:type
  bind:memo
  bind:created_at
  {loading}
  on:submit={handleTransactionSubmit}
  on:cancel={cancelEdit}
/>
<TransactionsTable
  {transactions}
  {accounts}
  bind:filter_account_id
  bind:filter_amount
  bind:filter_created_at
  bind:filter_memo
  {formatNumber}
  {loading}
  {error}
  on:edit={e => editTransaction(e.detail)}
  on:remove={e => removeTransaction(e.detail)}
  on:filter={filterTransactions}
/>

<style>
  :root {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen,
      Ubuntu, Cantarell, "Open Sans", "Helvetica Neue", sans-serif;
  }
  :global(body, html) {
    margin: 0;
    padding: 0;
  }
</style>

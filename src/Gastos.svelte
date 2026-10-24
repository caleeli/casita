<script>
import SummaryTable from './lib/SummaryTable.svelte';
import AccountsTable from './lib/AccountsTable.svelte';
import TransactionForm from './lib/TransactionForm.svelte';
import TransactionsTable from './lib/TransactionsTable.svelte';
import dayjs from "dayjs";

const API_BASE = import.meta.env.VITE_API_BASE;
export let fondo = "casita";
export let fondos = [];
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
  let annualReport = [];
  let annualTotals = { construction: 0, rest: 0 };
  let showAnnualReport = false;
  let annualReportLoading = false;
  let annualReportError = "";
  let movingTransaction = null;
  let moveTargets = [];
  let moveFondo = "";
  let moveAccounts = [];
  let moveAccountId = "";
  let moveLoading = false;
  let moveError = "";

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

  async function fetchAnnualReport() {
    try {
      annualReportLoading = true;
      annualReportError = "";
      const response = await fetch(api_base + "annual_report");
      if (!response.ok) throw new Error("Error cargando el reporte anual");
      annualReport = await response.json();
    } catch (e) {
      annualReportError = "Error cargando el reporte anual";
    } finally {
      annualReportLoading = false;
    }
  }

  async function fetchMoveAccounts() {
    if (!moveFondo) return;
    moveLoading = true;
    moveError = "";
    moveAccounts = [];
    moveAccountId = "";
    try {
      const response = await fetch(`${API_BASE}/${encodeURIComponent(moveFondo)}/accounts`);
      const result = await response.json();
      if (!response.ok) throw new Error(result.error || "No se pudieron cargar las cuentas");
      moveAccounts = result;
      const sourceAccountName = String(movingTransaction?.name || "").trim().toLowerCase();
      const matchingAccount = moveAccounts.find(
        (account) => String(account.name || "").trim().toLowerCase() === sourceAccountName
      );
      moveAccountId = matchingAccount ? matchingAccount.id : "";
    } catch (e) {
      moveError = e.message || "No se pudieron cargar las cuentas del fondo";
    } finally {
      moveLoading = false;
    }
  }

  async function openMoveDialog(transaction) {
    if (!moveTargets.length) {
      alert("No hay otro fondo disponible para mover este registro.");
      return;
    }
    movingTransaction = transaction;
    moveFondo = moveTargets[0].name;
    await fetchMoveAccounts();
  }

  async function moveTransaction() {
    if (!movingTransaction || !moveFondo || !moveAccountId) {
      moveError = "Seleccione el fondo y la cuenta de destino.";
      return;
    }

    const targetFund = moveTargets.find((item) => item.name === moveFondo);
    const targetAccount = moveAccounts.find((item) => String(item.id) === String(moveAccountId));
    if (!targetFund || !targetAccount) {
      moveError = "La cuenta seleccionada ya no está disponible.";
      return;
    }

    if (!confirm(`Mover “${movingTransaction.memo}” de ${fondo} a ${moveFondo}, cuenta ${targetAccount.name}?`)) return;

    try {
      moveLoading = true;
      moveError = "";
      const response = await fetch(api_base + "move_transaction/" + movingTransaction.id, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ target_fondo: moveFondo, account_id: moveAccountId }),
      });
      const result = await response.json().catch(() => ({}));
      if (!response.ok) throw new Error(result.error || `No se pudo mover el registro (${response.status})`);

      movingTransaction = null;
      await Promise.all([
        fetchAccounts(),
        fetchTransactions(),
        fetchSummary(),
        showAnnualReport ? fetchAnnualReport() : Promise.resolve(),
      ]);
    } catch (e) {
      moveError = e.message || "No se pudo mover el registro";
    } finally {
      moveLoading = false;
    }
  }

  function formatNumber(number) {
    if (!number) return "0";
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
      await Promise.all([
        fetchAccounts(),
        fetchTransactions(),
        showAnnualReport ? fetchAnnualReport() : Promise.resolve(),
      ]);
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
      await Promise.all([
        fetchAccounts(),
        fetchTransactions(),
        showAnnualReport ? fetchAnnualReport() : Promise.resolve(),
      ]);
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
      await Promise.all([
        fetchAccounts(),
        fetchTransactions(),
        showAnnualReport ? fetchAnnualReport() : Promise.resolve(),
      ]);
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

  $: if (fondo && showAnnualReport) fetchAnnualReport();
  $: moveTargets = fondos.filter((item) => !item.archived && item.name !== fondo);
  $: annualTotals = annualReport.reduce((totals, year) => {
    totals.construction += Number(year.construction) || 0;
    totals.rest += Number(year.rest) || 0;
    return totals;
  }, { construction: 0, rest: 0 });
</script>

<SummaryTable {summary} {formatNumber} />
<section class="annual-report">
  <button
    class="annual-report-toggle"
    type="button"
    aria-expanded={showAnnualReport}
    on:click={() => (showAnnualReport = !showAnnualReport)}
  >
    {showAnnualReport ? "Ocultar reporte anual" : "Ver reporte anual"}
  </button>

  {#if showAnnualReport}
    <div class="annual-report-content">
      <h2>Gastos por año</h2>
      {#if annualReportLoading}
        <p>Cargando reporte…</p>
      {:else if annualReportError}
        <p class="annual-report-error">{annualReportError}</p>
      {:else if annualReport.length}
        <div class="annual-report-table-wrap">
          <table>
            <thead>
              <tr>
                <th>Año</th>
                <th>Construcción de obra</th>
                <th>Resto</th>
                <th>Total</th>
              </tr>
            </thead>
            <tbody>
              {#each annualReport as year (year.year)}
                <tr>
                  <td>{year.year}</td>
                  <td>Bs. {formatNumber(year.construction)}</td>
                  <td>Bs. {formatNumber(year.rest)}</td>
                  <td>Bs. {formatNumber(Number(year.construction) + Number(year.rest))}</td>
                </tr>
              {/each}
            </tbody>
            <tfoot>
              <tr>
                <th>Total general</th>
                <td>Bs. {formatNumber(annualTotals.construction)}</td>
                <td>Bs. {formatNumber(annualTotals.rest)}</td>
                <td>Bs. {formatNumber(annualTotals.construction + annualTotals.rest)}</td>
              </tr>
            </tfoot>
          </table>
        </div>
      {:else}
        <p>No hay gastos registrados para mostrar.</p>
      {/if}
    </div>
  {/if}
</section>
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
  showMoveButton={moveTargets.length > 0}
  on:edit={e => editTransaction(e.detail)}
  on:remove={e => removeTransaction(e.detail)}
  on:move={e => openMoveDialog(e.detail)}
  on:filter={filterTransactions}
/>

{#if movingTransaction}
  <div class="move-dialog-backdrop">
    <section class="move-dialog" role="dialog" aria-modal="true" aria-labelledby="move-dialog-title">
      <h2 id="move-dialog-title">Mover registro a otro fondo</h2>
      <p class="move-transaction-description">
        {movingTransaction.memo} · Bs. {formatNumber(movingTransaction.amount)} · {movingTransaction.created_at}
      </p>

      <label for="move-fondo">Fondo de destino</label>
      <select id="move-fondo" bind:value={moveFondo} on:change={fetchMoveAccounts} disabled={moveLoading}>
        {#each moveTargets as target (target.name)}
          <option value={target.name}>{target.name}</option>
        {/each}
      </select>

      <label for="move-account">Cuenta de destino</label>
      <select id="move-account" bind:value={moveAccountId} disabled={moveLoading || !moveAccounts.length}>
        <option value="">Seleccione una cuenta</option>
        {#each moveAccounts as account (account.id)}
          <option value={account.id}>{account.name}</option>
        {/each}
      </select>

      {#if moveError}<p class="move-error">{moveError}</p>{/if}
      {#if !moveLoading && !moveError && !moveAccounts.length}
        <p>El fondo elegido no tiene cuentas disponibles.</p>
      {/if}

      <div class="move-dialog-actions">
        <button type="button" class="move-cancel" on:click={() => (movingTransaction = null)} disabled={moveLoading}>
          Cancelar
        </button>
        <button type="button" on:click={moveTransaction} disabled={moveLoading || !moveAccounts.length}>
          {moveLoading ? "Procesando…" : "Mover registro"}
        </button>
      </div>
    </section>
  </div>
{/if}

<style>
  :root {
    font-family: 'Segoe UI', 'Roboto', 'Helvetica Neue', Arial, sans-serif;
    background: #f6f8fa;
    color: #222;
  }
  :global(body, html) {
    margin: 0;
    padding: 0;
    background: #f6f8fa;
    width: 100vw;
  }
  :global(*) {
    box-sizing: border-box;
  }
  main, .main-content {
    max-width: 1100px;
    margin: 2rem auto;
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.07);
    padding: 2rem 2.5rem 2.5rem 2.5rem;
  }
  h1, h2, h3 {
    font-weight: 600;
    color: #2d3a4a;
    margin-bottom: 1rem;
  }
  hr {
    border: none;
    border-top: 1px solid #e0e4ea;
    margin: 2rem 0 1.5rem 0;
  }
  .annual-report {
    margin: 1.5rem 0;
  }
  .annual-report-toggle {
    background: #7b93fa;
    border: 0;
    border-radius: 6px;
    color: #fff;
    cursor: pointer;
    font-size: 1rem;
    padding: 0.7rem 1rem;
  }
  .annual-report-toggle:hover {
    background: #667eea;
  }
  .annual-report-content {
    margin-top: 1rem;
  }
  .annual-report-table-wrap {
    overflow-x: auto;
  }
  .annual-report table {
    border-collapse: collapse;
    min-width: 600px;
    width: 100%;
  }
  .annual-report th,
  .annual-report td {
    border-bottom: 1px solid #e0e4ea;
    padding: 0.8rem 1rem;
    text-align: right;
  }
  .annual-report th:first-child,
  .annual-report td:first-child {
    text-align: left;
  }
  .annual-report th {
    background: #f6f8fa;
    color: #2d3a4a;
  }
  .annual-report tfoot th,
  .annual-report tfoot td {
    background: #eef2ff;
    border-top: 2px solid #cbd5e1;
    font-weight: 700;
  }
  .annual-report-error {
    color: #b42318;
  }
  .move-dialog-backdrop {
    position: fixed;
    inset: 0;
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    background: rgba(15, 23, 42, 0.45);
  }
  .move-dialog {
    display: flex;
    flex-direction: column;
    gap: 0.7rem;
    width: min(100%, 460px);
    padding: 1.5rem;
    border-radius: 12px;
    background: #fff;
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.2);
  }
  .move-dialog h2,
  .move-dialog p {
    margin: 0;
  }
  .move-dialog label {
    margin-top: 0.4rem;
    font-weight: 600;
  }
  .move-dialog select {
    width: 100%;
    margin: 0;
    padding: 0.65rem;
  }
  .move-transaction-description {
    color: #475467;
  }
  .move-error {
    color: #b42318;
  }
  .move-dialog-actions {
    display: flex;
    justify-content: flex-end;
    gap: 0.6rem;
    margin-top: 0.6rem;
  }
  .move-dialog-actions .move-cancel {
    border: 1px solid #cbd5e1;
    background: #fff;
    color: #344054;
  }
  .move-dialog-actions .move-cancel:hover {
    background: #f1f5f9;
  }
</style>

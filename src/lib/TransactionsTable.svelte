<style>
  .transactions {
    width: 100%;
    margin-top: 1.5rem;
    background: #fafdff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    padding: 1.5rem 1.5rem 1rem 1.5rem;
    overflow: auto;
  }
  table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 1px 4px rgba(0,0,0,0.03);
  }
  table th, table td {
    padding: 0.7rem 0.6rem;
    font-size: 1em;
    border-bottom: 1px solid #e0e4ea;
    text-align: left;
  }
  table th {
    background: #f6f8fa;
    color: #4a5a6a;
    font-weight: 600;
    font-size: 1.02em;
    border-top: none;
  }
  table tr:last-child td {
    border-bottom: none;
  }
  .transaction-1 {
    text-align: right;
    color: #2d3a4a;
    font-weight: 600;
  }
  .transaction--1 {
    text-align: right;
    color: #e74c3c;
    font-weight: 600;
  }
  select, input[type="date"], input[type="text"], input[type="number"] {
    padding: 0.4rem 0.6rem;
    border: 1px solid #dbe3ea;
    border-radius: 5px;
    font-size: 1em;
    background: #fafdff;
    margin-right: 0.2rem;
    transition: border 0.2s;
  }
  select:focus, input:focus {
    border: 1.5px solid #6cb4f7;
    outline: none;
  }
  button[type="button"] {
    background: linear-gradient(90deg, #6cb4f7 0%, #4e9efc 100%);
    color: #fff;
    border: none;
    border-radius: 6px;
    padding: 0.4rem 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
  }
  button[type="button"]:hover {
    background: linear-gradient(90deg, #4e9efc 0%, #6cb4f7 100%);
  }
  button.table-row-button {
    padding: 0.2rem 0.7rem;
    color: #e74c3c!important;
    background: transparent !important;
    border: 1px solid gray!important;
    border-radius: 6px;
    font-size: 1.1em;
    margin-left: 0.2rem;
    cursor: pointer;
    width: 3rem;
    height: 2rem;
  }
  button.table-row-button:hover {
    background: #ffeaea;
    border: none;
  }
  .filter-account {
    width: 100%;
  }
  .filter-amount {
    width: 100%;
  }
</style>
<script>
  import { createEventDispatcher } from 'svelte';
  export let transactions = [];
  export let accounts = [];
  export let filter_account_id = "";
  export let filter_amount = "";
  export let filter_created_at = "";
  export let filter_memo = "";
  export let formatNumber;
  export let loading = false;
  export let error = "";

  const dispatch = createEventDispatcher();

  function handleEdit(transaction) {
    dispatch('edit', transaction);
  }
  function handleRemove(transaction) {
    dispatch('remove', transaction);
  }
  function handleFilter() {
    dispatch('filter');
  }
</script>

<div class="transactions">
  {#if loading}
    <div>Cargando...</div>
  {:else if error}
    <div style="color: red">{error}</div>
  {:else if transactions && transactions.length}
    <table>
      <tr>
        <th>#</th>
        <th>Cuenta</th>
        <th>Monto</th>
        <th>Fecha</th>
        <th>Descripción</th>
        <th></th>
      </tr>
      <tr>
        <th></th>
        <th>
          <select class="filter-account" bind:value={filter_account_id}>
            <option value="">-- Seleccione --</option>
            {#if accounts}
              {#each accounts as account}
                <option value={account.id}>{account.name}</option>
              {/each}
            {/if}
          </select>
        </th>
        <th>
          <input class="filter-amount" bind:value={filter_amount} />
        </th>
        <th>
          <input type="date" bind:value={filter_created_at} />
        </th>
        <th>
          <input bind:value={filter_memo} style="width: 95%" />
        </th>
        <th>
          <button type="button" on:click={handleFilter}>FILTRAR</button>
        </th>
      </tr>
      {#each transactions as transaction}
        <tr>
          <td>{transaction.id}</td>
          <td>{transaction.name}</td>
          <td class={`transaction-${transaction.type}`}>{formatNumber(transaction.amount)}</td>
          <td>{transaction.created_at}</td>
          <td>{transaction.memo}</td>
          <td>
            <button
              type="button"
              class="table-row-button"
              aria-label="Editar transacción"
              on:click={() => handleEdit(transaction)}
            >
              🖊️
            </button>
            <button
              type="button"
              class="table-row-button"
              aria-label="Eliminar transacción"
              on:click={() => handleRemove(transaction)}
            >
              x
            </button>
          </td>
        </tr>
      {/each}
    </table>
  {/if}
</div>

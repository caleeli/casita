<style>
  .transactions {
    width: calc(100% - 2rem);
  }
  table {
    width: 100%;
    border-collapse: collapse;
  }
  table th {
    text-align: left;
    border: 1px solid #ccc;
    font-size: 10pt;
  }
  table td {
    padding: 0.5rem;
    border: 1px solid #ccc;
    font-size: 10pt;
  }
  .transaction-1 {
    text-align: right;
  }
  .transaction--1 {
    text-align: right;
    color: red;
  }
  button.delete {
    padding: 0rem 0.5rem;
    color: red;
    background-color: transparent;
    border: 1px solid lightgray;
    border-radius: 4px;
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
  <hr />
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
          <select bind:value={filter_account_id}>
            <option value="">-- Seleccione --</option>
            {#if accounts}
              {#each accounts as account}
                <option value={account.id}>{account.name}</option>
              {/each}
            {/if}
          </select>
        </th>
        <th>
          <input bind:value={filter_amount} />
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
              class="delete"
              aria-label="Editar transacción"
              on:click={() => handleEdit(transaction)}
            >
              🖊️
            </button>
            <button
              type="button"
              class="delete"
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

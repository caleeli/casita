<script>
  import dayjs from "dayjs";

  const api_base = "https://callizaya.com/api.php/casita/";
  let account_id = "";
  let amount = "";
  let type = "-1";
  let memo = "";
  let created_at = dayjs().format("YYYY-MM-DD");
  let accounts = null;
  let transactions = null;
  // Fecth accounts from API
  async function fetchAccounts() {
    const response = await fetch(api_base + "accounts");
    accounts = await response.json();
  }
  // Fetch last 50 transactions from API
  async function fetchTransactions() {
    const response = await fetch(api_base + "transactions?page_size=50");
    transactions = await response.json();
  }
  function formatNumber(number) {
    return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
  }
  async function registerTransaction() {
    if (!account_id) {
      alert("Seleccione una cuenta");
      return;
    }
    const response = await fetch(api_base + "transaction", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        account_id,
        amount,
        type,
        memo,
        created_at,
      }),
    });
    const data = await response.json();
    account_id = "";
    amount = "";
    type = "-1";
    memo = "";
    created_at = dayjs().format("YYYY-MM-DD");
    fetchAccounts();
    fetchTransactions();
    return data;
  }
  async function removeTransaction(transaction) {
    if (!confirm(`Remover la transacción de ${transaction.memo} por Bs. ${transaction.amount}?`)) {
      return;
    }
    const response = await fetch(
      api_base + "remove_transaction/" + transaction.id,
      {
        method: "DELETE",
      }
    );
    const data = await response.json();
    fetchAccounts();
    fetchTransactions();
    account_id = transaction.account_id;
    amount = transaction.amount;
    type = transaction.type;
    memo = transaction.memo;
    created_at = transaction.created_at;
  }
  // LOAD
  fetchAccounts();
  fetchTransactions();
</script>

<main>
  {#if accounts}
    <div class="table">
      {#each accounts as account}
        <div><b>{account.name}:</b> {formatNumber(account.balance)}</div>
      {/each}
    </div>
  {/if}
  <form on:submit|preventDefault={registerTransaction}>
    <hr />
    <div>
      <label for="account">Cuenta</label>
      <select
        name="account_id"
        id="account_id"
        bind:value={account_id}
        required
      >
        <option value="">-- Seleccione --</option>
        {#if accounts}
          {#each accounts as account}
            <option value={account.id}>{account.name}</option>
          {/each}
        {/if}
      </select>
    </div>
    <div>
      <label for="memo">Fecha</label>
      <input
        type="date"
        name="created_at"
        id="created_at"
        required
        bind:value={created_at}
      />
    </div>
    <div>
      <label for="memo">Memo</label>
      <input name="memo" id="memo" required bind:value={memo} />
    </div>
    <div>
      <label for="amount">Monto</label>
      <input
        type="number"
        name="amount"
        id="amount"
        required
        bind:value={amount}
      />
    </div>
    <div>
      <label for="type">Tipo</label>
      <select name="type" id="type" bind:value={type}>
        <option value="1">Deposito</option>
        <option value="-1">Gasto</option>
      </select>
    </div>
    <div>
      <button type="submit">REGISTRAR TRANSACCIÓN</button>
    </div>
  </form>
  <div class="transactions">
    <hr />
    {#if transactions}
      <table>
        <tr>
          <th>Cuenta</th>
          <th>Monto</th>
          <th>Fecha</th>
          <th>Descripción</th>
          <th />
        </tr>
        {#each transactions as transaction}
          <tr>
            <td>{transaction.name}</td>
            <td class={`transaction-${transaction.type}`}
              >{formatNumber(transaction.amount)}</td
            >
            <td>{transaction.created_at}</td>
            <td>{transaction.memo}</td>
            <td
              ><button
                type="button"
                class="delete"
                on:click={() => removeTransaction(transaction)}>x</button
              ></td
            >
          </tr>
        {/each}
      </table>
    {/if}
  </div>
</main>

<style>
  :root {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen,
      Ubuntu, Cantarell, "Open Sans", "Helvetica Neue", sans-serif;
  }
  :global(body, html) {
    margin: 0;
    padding: 0;
  }

  main {
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    width: 100vw;
    align-items: center;
    justify-content: start;
  }

  form {
    width: calc(100% - 2rem);
  }
  form div {
    display: flex;
    margin-bottom: 0.5rem;
  }
  div label {
    display: block;
    width: 100px;
  }
  div select {
    flex-grow: 1;
    padding: 0.5rem 0;
  }
  div input {
    flex-grow: 1;
    padding: 0.5rem 0;
  }
  button[type="submit"] {
    padding: 1rem;
    width: 100%;
  }
  button.delete {
    padding: 0rem 0.5rem;
    color: red;
    background-color: transparent;
    border: 1px solid lightgray;
    border-radius: 4px;
  }
  div.table {
    width: calc(100% - 2rem);
    display: grid;
    grid-template-columns: 1fr 1fr;
    text-align: right;
  }
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
</style>

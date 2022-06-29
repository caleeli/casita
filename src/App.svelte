<script>
  import dayjs from "dayjs";

  const api_base = "https://callizaya.com/api.php/casita/";
  let account_id = "1";
  let amount = "";
  let type = "1";
  let memo = "";
  let created_at = dayjs().format("YYYY-MM-DD");
  // Fecth accounts from API
  async function fetchAccounts() {
    const response = await fetch(api_base + "accounts");
    const data = await response.json();
    return data;
  }
  // Fetch last 10 transactions from API
  async function fetchTransactions() {
    const response = await fetch(api_base + "transactions?page_size=10");
    const data = await response.json();
    return data;
  }
  function formatNumber(number) {
    return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
  }
  async function registerTransaction() {
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
    account_id = "1";
    amount = "";
    type = "1";
    memo = "";
    created_at = dayjs().format("YYYY-MM-DD");
    return data;
  }
</script>

<main>
  {#await fetchAccounts()}
    ...
  {:then accounts}
    <div class="table">
      {#each accounts as account}
        <div><b>{account.name}:</b> {formatNumber(account.balance)}</div>
      {/each}
    </div>
  {:catch error}
    <b>Error</b> {error}
  {/await}
  <div class="transactions">
    <hr />
    {#await fetchTransactions()}
      ...
    {:then transactions}
      <table>
        <tr>
          <th>Cuenta</th>
          <th>Monto</th>
          <th>Fecha</th>
          <th>Descripción</th>
        </tr>
        {#each transactions as transaction}
          <tr>
            <td>{transaction.name}</td>
            <td class={`transaction-${transaction.type}`}
              >{formatNumber(transaction.amount)}</td
            >
            <td>{transaction.created_at}</td>
            <td>{transaction.memo}</td>
          </tr>
        {/each}
      </table>
    {:catch error}
      <b>Error</b> {error}
    {/await}
    <hr />
  </div>
  <form on:submit|preventDefault={registerTransaction}>
    <div>
      <label for="account">Cuenta</label>
      <select name="account_id" id="account_id" bind:value={account_id}>
        {#await fetchAccounts()}
          ...
        {:then accounts}
          {#each accounts as account}
            <option value={account.id}>{account.name}</option>
          {/each}
        {:catch error}
          <b>Error</b> {error}
        {/await}
      </select>
    </div>
    <div>
      <label for="amount" required>Monto</label>
      <input type="number" name="amount" id="amount" bind:value={amount} />
    </div>
    <div>
      <label for="type">Tipo</label>
      <select name="type" id="type" bind:value={type}>
        <option value="1">Deposito</option>
        <option value="-1">Retiro</option>
      </select>
    </div>
    <div>
      <label for="memo">Memo</label>
      <textarea
        name="memo"
        id="memo"
        cols="30"
        rows="3"
        required
        bind:value={memo}
      />
    </div>
    <div>
      <label for="memo">Fecha</label>
      <input
        type="date"
        name="created_at"
        id="created_at"
        bind:value={created_at}
      />
    </div>
    <div>
      <button type="submit">REGISTRAR TRANSACCIÓN</button>
    </div>
  </form>
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
    height: 100vh;
    align-items: center;
    justify-content: start;
    overflow: hidden;
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
  div textarea {
    flex-grow: 1;
  }
  button {
    padding: 1rem;
    width: 100%;
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
  }
  table td {
    padding: 0.5rem;
    border: 1px solid #ccc;
  }
</style>

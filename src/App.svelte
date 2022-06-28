<script>
  const api_base = "https://callizaya.com/api.php/casita/";
  // Fecth accounts from API
  async function fetchAccounts() {
    const response = await fetch(api_base + "accounts");
    const data = await response.json();
    return data;
  }
</script>

<main>
  {#await fetchAccounts()}
    ...
  {:then accounts}
    <table border="1">
      {#each accounts as account}
        <tr>
          <th>{account.name}</th>
          <td>{account.balance}</td>
        </tr>
      {/each}
    </table>
  {:catch error}
    <b>Error</b> {error}
  {/await}
</main>

<style>
  :root {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen,
      Ubuntu, Cantarell, "Open Sans", "Helvetica Neue", sans-serif;
  }

  main {
    text-align: center;
    padding: 1em;
    margin: 0 auto;
  }

  table {
    margin: 0 auto;
    border-collapse: collapse;
    width: 100%;
  }
</style>

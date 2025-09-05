<style>
  .table {
    width: calc(100% - 2rem);
    display: grid;
    grid-template-columns: 1fr 1fr;
    text-align: right;
  }
</style>
<script>
  export let accounts = [];
  export let formatNumber;
</script>

{#if accounts}
  <div class="table">
    {#each accounts as account}
      <div><b>{account.name}:</b> {formatNumber(account.balance)}</div>
    {/each}
  </div>
{/if}

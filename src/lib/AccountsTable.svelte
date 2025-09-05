<style>
  .accounts-table {
    width: 100%;
    display: flex;
    gap: 2.5rem;
    margin-bottom: 1.5rem;
    font-size: 1.05rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #e0e4ea;
    flex-wrap: wrap;
  }
  .account-row {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    min-width: 120px;
  }
  .account-name {
    color: #6c7a89;
    font-weight: 500;
    font-size: 0.98em;
  }
  .account-balance {
    font-weight: 600;
    color: #2d3a4a;
    font-size: 1.12em;
  }
</style>
<script>
  export let accounts = [];
  export let formatNumber;
</script>

{#if accounts}
  <div class="accounts-table">
    {#each accounts as account}
      <div class="account-row">
        <span class="account-name">{account.name}:</span>
        <span class="account-balance">{formatNumber(account.balance)}</span>
      </div>
    {/each}
  </div>
{/if}

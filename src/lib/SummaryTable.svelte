<script>
  export let summary = [];
  export let formatNumber;
</script>

{#if summary}
  <div class="summary-table">
    {#each summary as record}
      <div class="summary-row">
        <span class="summary-name">{record.name}:</span>
        <span class="summary-balance">{formatNumber(record.balance)}</span>
      </div>
    {/each}
  </div>
{/if}

<style>
  .summary-table {
    display: flex;
    gap: 2.5rem;
    margin-bottom: 1.5rem;
    padding: 1rem 0 0.5rem 0;
    border-bottom: 1px solid #e0e4ea;
    font-size: 1.1rem;
  }
  .summary-row {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    min-width: 120px;
  }
  .summary-name {
    color: #6c7a89;
    font-weight: 500;
    font-size: 0.98em;
  }
  .summary-balance {
    font-weight: 600;
    color: #2d3a4a;
    font-size: 1.15em;
  }
</style>

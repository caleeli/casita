<script>
  export let summary = [];
  export let formatNumber;
</script>

{#if summary}
  <div class="table">
    {#each summary as record}
      <div><b>{record.name}:</b> {formatNumber(record.balance)}</div>
    {/each}
  </div>
{/if}

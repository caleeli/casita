<style>
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
button.edit {
  background-color: lightgreen;
}
button.cancel {
  background-color: lightsalmon;
}
</style>
<script>
  import { createEventDispatcher } from 'svelte';
  export let accounts = [];
  export let transaction_id = "";
  export let account_id = "";
  export let amount = "";
  export let type = "-1";
  export let memo = "";
  export let created_at = "";
  export let loading = false;

  const dispatch = createEventDispatcher();

  function handleSubmit(e) {
    e.preventDefault();
    dispatch('submit');
  }
  function handleCancel() {
    dispatch('cancel');
  }
</script>

<form on:submit={handleSubmit}>
  <hr />
  <div>
    <label for="account">Cuenta</label>
    <select name="account_id" id="account_id" bind:value={account_id} required>
      <option value="">-- Seleccione --</option>
      {#if accounts}
        {#each accounts as account}
          <option value={account.id}>{account.name}</option>
        {/each}
      {/if}
    </select>
  </div>
  <div>
    <label for="created_at">Fecha</label>
    <input type="date" name="created_at" id="created_at" required bind:value={created_at} />
  </div>
  <div>
    <label for="memo">Memo</label>
    <input name="memo" id="memo" required bind:value={memo} />
  </div>
  <div>
    <label for="amount">Monto</label>
    <input type="number" name="amount" id="amount" required bind:value={amount} />
  </div>
  <div>
    <label for="type">Tipo</label>
    <select name="type" id="type" bind:value={type}>
      <option value="1">Deposito</option>
      <option value="-1">Gasto</option>
    </select>
  </div>
  <div>
    {#if transaction_id}
      <button type="submit" class="edit" disabled={loading}>ACTUALIZAR TRANSACCIÓN</button>
      <button type="button" class="cancel" on:click={handleCancel} disabled={loading}>CANCELAR EDICIÓN</button>
    {:else}
      <button type="submit" class="create" disabled={loading}>REGISTRAR TRANSACCIÓN</button>
    {/if}
  </div>
</form>

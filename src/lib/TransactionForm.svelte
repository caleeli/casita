<style>
form {
  width: 100%;
  background: #fafdff;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.04);
  padding: 1.5rem 1.5rem 1rem 1.5rem;
  margin-bottom: 2rem;
}
form div {
  display: flex;
  align-items: center;
  margin-bottom: 1rem;
}
div label {
  display: block;
  width: 110px;
  color: #4a5a6a;
  font-weight: 500;
  font-size: 1em;
}
div select, div input {
  flex-grow: 1;
  padding: 0.6rem 0.8rem;
  border: 1px solid #dbe3ea;
  border-radius: 6px;
  font-size: 1em;
  background: #fff;
  margin-left: 0.5rem;
  transition: border 0.2s;
}
div select:focus, div input:focus {
  border: 1.5px solid #6cb4f7;
  outline: none;
}
button[type="submit"], button.cancel, button.create {
  padding: 0.9rem 1.5rem;
  width: 100%;
  border: none;
  border-radius: 8px;
  font-size: 1.08em;
  font-weight: 600;
  background: linear-gradient(90deg, #6cb4f7 0%, #4e9efc 100%);
  color: #fff;
  cursor: pointer;
  box-shadow: 0 2px 8px rgba(76, 175, 255, 0.08);
  margin-bottom: 0.5rem;
  transition: background 0.2s, box-shadow 0.2s;
}
button[type="submit"]:hover, button.create:hover {
  background: linear-gradient(90deg, #4e9efc 0%, #6cb4f7 100%);
  box-shadow: 0 4px 16px rgba(76, 175, 255, 0.13);
}
button.edit {
  background: linear-gradient(90deg, #7ed957 0%, #4caf50 100%);
  color: #fff;
}
button.edit:hover {
  background: linear-gradient(90deg, #4caf50 0%, #7ed957 100%);
}
button.cancel {
  background: linear-gradient(90deg, #ffb199 0%, #ff0844 100%);
  color: #fff;
}
button.cancel:hover {
  background: linear-gradient(90deg, #ff0844 0%, #ffb199 100%);
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

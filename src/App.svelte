<script>
  import Gastos from "./Gastos.svelte";
  let fondo = window.location.hash === "#/hogar" ? "hogar" : "casita";
</script>

<main>
  <nav>
    <button class={fondo === "casita" ? "active" : "inactive"} on:click={() => fondo="casita"}>Construcción</button>
    <button class={fondo === "hogar" ? "active" : "inactive"} on:click={() => fondo="hogar"}>Gastos hogar</button>
  </nav>
  <Gastos {fondo} />
</main>

<style>
  button {
    text-transform: uppercase;
    padding: 0.5rem 1rem;
    border: 1px solid lightgray;
    background-color: #eee;
  }
  button.active {
    border: 1px solid #ccc;
    font-weight: bold;
    background-color: #ccc;
  }
</style>

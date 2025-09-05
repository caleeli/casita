<script>
  import Gastos from "./Gastos.svelte";
  // Fondos existentes (simulado, en real podría venir de API/localStorage)
  let fondos = [
    { nombre: "casita", archivado: false },
    { nombre: "hogar", archivado: false }
  ];
  let fondo = "";
  let nuevoFondo = "";
  let correo = "";
  
  function seleccionarFondo(nombre) {
    fondo = nombre;
  }
  function crearFondo() {
    if (nuevoFondo && !fondos.find(f => f.nombre === nuevoFondo)) {
      fondos = [...fondos, { nombre: nuevoFondo, archivado: false }];
      fondo = nuevoFondo;
      nuevoFondo = "";
    }
  }
  function archivarFondo(nombre) {
    fondos = fondos.map(f => f.nombre === nombre ? { ...f, archivado: true } : f);
    // Selecciona otro fondo activo si el actual se archiva
    if (fondo === nombre) {
      const activo = fondos.find(f => !f.archivado && f.nombre !== nombre);
      fondo = activo ? activo.nombre : "";
    }
  }
  function compartirFondo(nombre) {
    if (correo) {
      // Aquí iría la lógica real de compartir (API, mailto, etc)
      alert(`Fondo '${nombre}' compartido con ${correo}`);
      correo = "";
    }
  }
</script>


<main>
  {#if !fondo}
    <section>
      <h2>Fondos</h2>
      {#each fondos as f (f.nombre)}
        {#if !f.archivado}
          <button class="fondosel" on:click={() => seleccionarFondo(f.nombre)}>{f.nombre}</button>
          <br>
        {/if}
      {/each}
      <div style="margin-top:1em">
        <input type="text" placeholder="Nuevo fondo" bind:value={nuevoFondo} />
        <button on:click={crearFondo}>Crear fondo</button>
      </div>
    </section>
  {:else}
    <nav style="margin-bottom:1em">
      {#each fondos as f (f.nombre)}
        {#if !f.archivado}
          <button class={fondo === f.nombre ? "active" : "inactive"} on:click={() => seleccionarFondo(f.nombre)}>{f.nombre}</button>
        {/if}
      {/each}
      <button style="margin-left:2em" on:click={() => fondo = ""}>🏠</button>
    </nav>
    <Gastos {fondo} />
  {/if}
</main>

<style>
  button {
    text-transform: uppercase;
    padding: 0.5rem 1rem;
    border: 1px solid lightgray;
    background-color: #eee;
    margin-bottom: 2px;
  }
  .fondosel {
    font-size: 1.1em;
    margin-right: 0.5em;
    margin-bottom: 0.5em;
  }
  button.active {
    border: 1px solid #ccc;
    font-weight: bold;
    background-color: #ccc;
  }
  nav {
    margin-bottom: 1.5em;
  }
  input[type="text"], input[type="email"] {
    padding: 0.3em;
    margin-right: 0.3em;
    border: 1px solid #ccc;
    border-radius: 3px;
  }
</style>

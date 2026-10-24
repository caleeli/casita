<script>
  import Gastos from "./Gastos.svelte";
  import List from "./lib/fondos/List.svelte";
  import { openLogin, onLogin, api, logout } from "./lib/login";

  let user;
  let token;
  // Fondos existentes (simulado, en real podría venir de API/localStorage)
  let fondos = [];
  let fondo = "";
  let nuevoFondo = "";
  let correo = "";

  // Login handler
  onLogin(async (login) => {
    user = login.user;
    token = login.token;
    if (!token) {
      fondos = [];
      fondo = "";
      nuevoFondo = "";
      correo = "";
      return;
    }
    fondos = await api("fondos/man/fondos");
  });
  
  function seleccionarFondo(name) {
    fondo = name;
  }
  function crearFondo() {
    if (nuevoFondo && !fondos.find(f => f.name === nuevoFondo)) {
      fondos = [...fondos, { name: nuevoFondo, archived: false }];
      fondo = nuevoFondo;
      nuevoFondo = "";
    }
  }
  function archivarFondo(name) {
    fondos = fondos.map(f => f.name === name ? { ...f, archived: true } : f);
    // Selecciona otro fondo activo si el actual se archiva
    if (fondo === name) {
      const activo = fondos.find(f => !f.archived && f.name !== name);
      fondo = activo ? activo.name : "";
    }
  }
  function compartirFondo(name) {
    if (correo) {
      // Aquí iría la lógica real de compartir (API, mailto, etc)
      alert(`Fondo '${name}' compartido con ${correo}`);
      correo = "";
    }
  }
</script>

<main>
  {#if token === ""}
    <section class="login-container">
      <div class="login-card">
        <img src="https://developers.google.com/identity/images/g-logo.png" alt="Google" class="google-logo" />
        <h2>Bienvenido a Casita</h2>
        <p>Inicia sesión para continuar</p>
        <button class="google-btn" on:click={openLogin}>
          <img src="https://developers.google.com/identity/images/g-logo.png" alt="Google" class="btn-logo" />
          Iniciar sesión con Google
        </button>
      </div>
    </section>
  {:else}
    <header class="user-header">
      <img class="user-avatar" src={user.picture} alt={user.name} />
      <div class="user-info">
        <div class="user-name">{user.name}</div>
        <div class="user-email">{user.email}</div>
      </div>
      <button class="logout-btn" type="button" on:click={logout}>Cerrar sesión</button>
    </header>
    {#if !fondo}
      <section class="fondos-section">
        <h2>Mis Fondos</h2>
        <div class="fondos-list">
          <List {fondos} {seleccionarFondo} />
        </div>
        <div class="nuevo-fondo">
          <input type="text" placeholder="Nuevo fondo" bind:value={nuevoFondo} />
          <button class="crear-btn" on:click={crearFondo}>Crear fondo</button>
        </div>
      </section>
    {:else}
      <nav class="fondos-nav">
        {#each fondos as f (f.name)}
          {#if !f.archived}
            <button
              class={fondo === f.name ? "active fondo-btn" : "inactive fondo-btn"}
              on:click={() => seleccionarFondo(f.name)}>{f.name}</button>
          {/if}
        {/each}
        <button class="home-btn" on:click={() => (fondo = "")}>🏠</button>
      </nav>
      <Gastos {fondo} {fondos} />
    {/if}
  {/if}
</main>

<style>
  main {
    min-height: 100vh;
    background: linear-gradient(135deg, #f8fafc 0%, #e0e7ef 100%);
    padding-bottom: 2em;
  }
  .login-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 80vh;
  }
  .login-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 4px 24px 0 #0001;
    padding: 2.5em 2em 2em 2em;
    display: flex;
    flex-direction: column;
    align-items: center;
    min-width: 320px;
  }
  .google-logo {
    width: 48px;
    margin-bottom: 1em;
  }
  .google-btn {
    display: flex;
    align-items: center;
    gap: 0.7em;
    background: #fff;
    border: 1px solid #dadce0;
    border-radius: 4px;
    font-size: 1.1em;
    font-weight: 500;
    color: #444;
    padding: 0.6em 1.5em;
    cursor: pointer;
    box-shadow: 0 2px 4px #0001;
    transition: background 0.2s;
    margin-top: 1.2em;
  }
  .google-btn:hover {
    background: #f1f3f4;
  }
  .btn-logo {
    width: 22px;
    height: 22px;
  }
  .user-header {
    display: flex;
    align-items: center;
    gap: 1em;
    flex-wrap: wrap;
    background: #fff;
    border-radius: 0 0 16px 16px;
    box-shadow: 0 2px 12px #0001;
    padding: 1.2em 2em 1.2em 1.5em;
    margin-bottom: 2em;
  }
  .user-avatar {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    border: 2px solid #e0e7ef;
    object-fit: cover;
    background: #f3f4f6;
  }
  .user-info {
    display: flex;
    flex-direction: column;
    gap: 0.2em;
  }
  .user-name {
    font-size: 1.2em;
    font-weight: 600;
    color: #222;
  }
  .user-email {
    font-size: 0.98em;
    color: #666;
  }
  .logout-btn {
    margin-left: auto;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    padding: 0.6em 0.9em;
    background: #fff;
    color: #344054;
    font-size: 0.95em;
    cursor: pointer;
  }
  .logout-btn:hover {
    background: #f1f5f9;
  }
  .fondos-section {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 2px 12px #0001;
    max-width: 820px;
    margin: 2em auto 0 auto;
    padding: 2em 2em 1.5em 2em;
    display: flex;
    flex-direction: column;
    align-items: center;
  }
  .fondos-section h2 {
    margin-bottom: 1.2em;
    color: #2a2a2a;
    font-weight: 600;
  }
  .fondos-list {
    display: flex;
    flex-wrap: wrap;
    gap: 0.7em;
    justify-content: center;
    margin-bottom: 1.2em;
  }
  .fondosel, .fondo-btn {
    font-size: 1.08em;
    padding: 0.5em 1.2em;
    border-radius: 6px;
    border: 1px solid #cbd5e1;
    background: #f1f5f9;
    color: #222;
    margin: 0;
    transition: background 0.2s, border 0.2s;
    cursor: pointer;
  }
  .fondosel:hover, .fondo-btn:hover {
    background: #e0e7ef;
    border: 1px solid #a3bffa;
  }
  .fondosel.active, .fondo-btn.active {
    background: #a3bffa;
    color: #fff;
    border: 1.5px solid #7b93fa;
    font-weight: bold;
  }
  .fondosel.inactive, .fondo-btn.inactive {
    opacity: 0.7;
  }
  .nuevo-fondo {
    margin-top: 1.2em;
    display: flex;
    gap: 0.5em;
    width: 100%;
    justify-content: center;
  }
  .crear-btn {
    background: #7b93fa;
    color: #fff;
    border: none;
    border-radius: 4px;
    padding: 0.45em 1.1em;
    font-size: 1em;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.2s;
  }
  .crear-btn:hover {
    background: #4f6cf7;
  }
  .fondos-nav {
    margin-bottom: 1.5em;
    display: flex;
    gap: 0.7em;
    align-items: center;
    flex-wrap: wrap;
  }
  .home-btn {
    margin-left: 1.5em;
    font-size: 1.2em;
    background: #fff;
    border: 1px solid #cbd5e1;
    border-radius: 50%;
    width: 2.2em;
    height: 2.2em;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 1px 4px #0001;
    transition: background 0.2s;
  }
  .home-btn:hover {
    background: #e0e7ef;
  }
  input[type="text"],
  input[type="email"] {
    padding: 0.45em 0.7em;
    margin-right: 0.3em;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    font-size: 1em;
    background: #f8fafc;
    outline: none;
    transition: border 0.2s;
  }
  input[type="text"]:focus,
  input[type="email"]:focus {
    border: 1.5px solid #7b93fa;
  }
</style>

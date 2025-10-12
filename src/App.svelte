<script>
  import Gastos from "./Gastos.svelte";
  import { openLogin, onLogin } from "https://callizaya.com/api.php/auth/client.js";

  // Obtiene el token de localStorage
  let token = localStorage.getItem("token") || "";
  // Obtiene datos del usuario desde localStorage (simulado aquí)
  let userStored = JSON.parse(localStorage.getItem("user") || "{}");
  let user = {
    email: userStored.email || "",
    familyName: userStored.familyName || "",
    gender: userStored.gender || null,
    givenName: userStored.givenName || "",
    hd: userStored.hd || null,
    id: userStored.id || "",
    link: userStored.link || null,
    locale: userStored.locale || null,
    name: userStored.name || "",
    picture: userStored.picture || "",
    verifiedEmail: userStored.verifiedEmail || true
  };
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
  // Listen for login events
  onLogin(({ user: newUser, token: newToken }) => {
    token = newToken;
    user = newUser;
    // Guarda en localStorage
    localStorage.setItem("token", token);
    localStorage.setItem("user", JSON.stringify(user));
  });
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
    </header>
    {#if !fondo}
      <section class="fondos-section">
        <h2>Mis Fondos</h2>
        <div class="fondos-list">
          {#each fondos as f (f.nombre)}
            {#if !f.archivado}
              <button class="fondosel fondo-btn" on:click={() => seleccionarFondo(f.nombre)}>{f.nombre}</button>
            {/if}
          {/each}
        </div>
        <div class="nuevo-fondo">
          <input type="text" placeholder="Nuevo fondo" bind:value={nuevoFondo} />
          <button class="crear-btn" on:click={crearFondo}>Crear fondo</button>
        </div>
      </section>
    {:else}
      <nav class="fondos-nav">
        {#each fondos as f (f.nombre)}
          {#if !f.archivado}
            <button
              class={fondo === f.nombre ? "active fondo-btn" : "inactive fondo-btn"}
              on:click={() => seleccionarFondo(f.nombre)}>{f.nombre}</button>
          {/if}
        {/each}
        <button class="home-btn" on:click={() => (fondo = "")}>🏠</button>
      </nav>
      <Gastos {fondo} />
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
  .fondos-section {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 2px 12px #0001;
    max-width: 420px;
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

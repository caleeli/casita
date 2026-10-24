<script>
    import { api } from "../login";

    export let fondos = [];
    export let seleccionarFondo;

    let correo = "";

    function compartirFondo(name) {
        if (correo) {
            api("fondos/man/compartir", {
                method: "POST",
                body: JSON.stringify({ name, email: correo }),
            }).then((res) => {
                if (res.success) {
                    alert(`Fondo '${name}' compartido con ${correo}`);
                    correo = "";
                } else {
                    alert(`Error al compartir: ${res.error}`);
                }
            });
        }
    }
</script>

<table class="fondos-list">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Compartir</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        {#each fondos as f}
            <tr class="border-t">
                <td class="p-4">{f.name}</td>
                <td class="p-4">
                    <input
                        type="email"
                        bind:value={correo}
                        placeholder="Correo para compartir"
                        class="w-full mb-2 p-1 border rounded"
                    />
                    <button 
                        class="w-full fondo-btn bg-blue-500 hover:bg-blue-600 text-white"
                        on:click={() => compartirFondo(f.name)}
                    >
                        Compartir
                    </button>
                </td>
                <td class="p-4">
                    <button
                        class="fondosel fondo-btn"
                        on:click={() => seleccionarFondo(f.name)}
                    >
                        Abrir
                    </button>
                </td>
            </tr>
        {/each}
    </tbody>
</table>

<style scoped>
    .fondos-list {
        width: 100%;
        border-collapse: collapse;
        margin: 2rem 0;
    }

    th, td {
        padding: 1rem;
        border-top: 1px solid #ddd;
    }

    th {
        background-color: #f5f5f5;
        font-weight: bold;
        text-align: left;
    }

    tr:hover {
        background-color: #f9f9f9;
    }

    .fondosel,
    .fondo-btn {
        font-size: 1em;
        padding: 0.5em 1em;
        border-radius: 4px;
        border: 1px solid #cbd5e1;
        background: #f1f5f9;
        color: #222;
        margin: 0;
        transition:
            background 0.2s,
            border 0.2s;
        cursor: pointer;
    }

    .w-full {
        width: 100%;
    }

    .mb-2 {
        margin-bottom: 0.5rem;
    }
</style>

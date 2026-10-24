
import { openLogin, onLogin as baseOnLogin } from "https://callizaya.com/api.php/auth/client.js";
export { openLogin };

// Obtiene el token de localStorage
export let token = localStorage.getItem("token") || "";

// Obtiene datos del usuario desde localStorage (simulado aquí)
let userStored = JSON.parse(localStorage.getItem("user") || "{}");
export let user = {
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

const listeners = [];
export function onLogin(callback) {
  listeners.push(callback);
  callback({ user, token });
}

// Listen for login events
baseOnLogin((login) => {
  user = login.user;
  token = login.jwt;
  // Guarda en localStorage
  localStorage.setItem("token", token);
  localStorage.setItem("user", JSON.stringify(user));
  listeners.forEach((callback) => callback({ user, token }));
});

// Función para hacer llamadas a la API con el token
export async function api(endpoint, options = {}) {
  if (!token) {
    throw new Error("No token available. Please log in.");
  }

  const response = await fetch(`https://callizaya.com/api.php/${endpoint}`, {
    ...options,
    headers: {
      ...options.headers,
      Authorization: `Bearer ${token}`,
      "Content-Type": "application/json"
    }
  });

  if (!response.ok) {
    throw new Error(`API request failed with status ${response.status}`);
  }

  return response.json();
}


import { openLogin, onLogin as baseOnLogin } from "https://callizaya.com/api.php/auth/client.js";
export { openLogin };

// Obtiene el token de localStorage
export let token = localStorage.getItem("token") || "";
if (!token) {
  localStorage.removeItem("token");
  localStorage.removeItem("user");
}

// Obtiene datos del usuario desde localStorage (simulado aquí)
let userStored = JSON.parse(token ? localStorage.getItem("user") || "{}" : "{}");
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
const emptyUser = () => ({
  email: "",
  familyName: "",
  gender: null,
  givenName: "",
  hd: null,
  id: "",
  link: null,
  locale: null,
  name: "",
  picture: "",
  verifiedEmail: false
});

function notifyListeners() {
  listeners.forEach((callback) => callback({ user, token }));
}

export function onLogin(callback) {
  listeners.push(callback);
  callback({ user, token });
}

export function logout() {
  token = "";
  user = emptyUser();
  localStorage.removeItem("token");
  localStorage.removeItem("user");
  notifyListeners();
}

// Listen for login events
baseOnLogin((login) => {
  token = login?.jwt || "";
  if (token) {
    user = login.user;
    localStorage.setItem("token", token);
    localStorage.setItem("user", JSON.stringify(user));
  } else {
    user = emptyUser();
    localStorage.removeItem("token");
    localStorage.removeItem("user");
  }
  notifyListeners();
});

// Keep the current tab in sync when another tab clears the session.
window.addEventListener("storage", (event) => {
  if ((event.key === "token" || event.key === null) && !event.newValue) {
    logout();
  }
});

// Función para hacer llamadas a la API con el token
export async function api(endpoint, options = {}) {
  const storedToken = localStorage.getItem("token") || "";
  if (!storedToken) {
    logout();
    throw new Error("No token available. Please log in.");
  }
  token = storedToken;

  const response = await fetch(`https://callizaya.com/api.php/${endpoint}`, {
    ...options,
    headers: {
      ...options.headers,
      Authorization: `Bearer ${storedToken}`,
      "Content-Type": "application/json"
    }
  });

  if (response.status === 401) {
    logout();
    throw new Error("Session expired. Please log in again.");
  }

  if (!response.ok) {
    throw new Error(`API request failed with status ${response.status}`);
  }

  return response.json();
}

import { CONFIG } from "../Utils/config.js";
import Utility from "./Utility.js";

/**
 * Session.js
 * Handles user session management, app data caching, and encryption utilities.
 *
 * Dependencies:
 * - CONFIG.js
 * - Utility.js
 * - jwt-decode (assumed available globally or imported)
 */

/**
 * Fetch the encryption key from server for app data encryption
 * @returns {Promise<Object|null>} Encryption key object or null on failure
 */
export async function fetchEncryptionKey() {
  try {
    const response = await fetch(`${CONFIG.BASE_URL}/public/set-session.php`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-Requested-With": "XMLHttpRequest",
      },
      body: JSON.stringify({ action: "config" }),
    });
    return await response.json();
  } catch (error) {
    console.warn("⚠️ Failed to fetch encryption key:", error);
    return null;
  }
}

/**
 * Start a new user session (JS + PHP session)
 * @param {string} token - JWT token
 * @returns {Promise<Object|null>} Server response or null on failure
 */
export async function startNewSession(token) {
  try {
    if (!token) throw new Error("Token is required to start a session.");

    const decryptToken = jwt_decode(token);
    const { userid, email, role } = decryptToken;

    // Store JS session
    sessionStorage.setItem(CONFIG.TOKEN_KEY_NAME, token);
    sessionStorage.setItem("user", JSON.stringify({ role, userid }));

    // Store PHP session
    const response = await fetch(`${CONFIG.BASE_URL}/public/set-session.php`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-Requested-With": "XMLHttpRequest",
      },
      body: JSON.stringify({ action: "set", token, role, userid }),
    });

    return await response.json();
  } catch (error) {
    console.warn("⚠️ Failed to start user session:", error);
    return null;
  }
}

/**
 * Destroy the current session (JS + PHP)
 */
export async function destroyCurrentSession() {
  try {
    const response = await fetch(`${CONFIG.BASE_URL}/public/set-session.php`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-Requested-With": "XMLHttpRequest",
      },
      body: JSON.stringify({ action: "clear" }),
    });

    const data = await response.json();
    if (data.success) {
      sessionStorage.clear();
      window.location.href = `${CONFIG.BASE_URL}/auth/login?f-bk=logout`;
    }
  } catch (error) {
    console.warn("⚠️ Failed to destroy user session:", error);
  }
}

/**
 * Decode JWT token from session storage
 * @returns {Promise<Object>} Decoded token payload
 */
export async function decryptJsToken() {
  const storedToken = sessionStorage.getItem(CONFIG.TOKEN_KEY_NAME);
  if (!storedToken) throw new Error("No token found in sessionStorage");

  try {
    return jwt_decode(storedToken);
  } catch (error) {
    console.warn("⚠️ Invalid token format:", error);
    return null;
  }
}

/**
 * Clear PHP profile session (partial session)
 * @returns {Promise<Object>} Server response
 */
export async function clearPHPProfileSession() {
  try {
    const response = await fetch(`${CONFIG.BASE_URL}/public/set-session.php`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-Requested-With": "XMLHttpRequest",
      },
      body: JSON.stringify({ action: "unset-p" }),
    });
    return await response.json();
  } catch (error) {
    console.warn("⚠️ Failed to clear PHP profile session:", error);
    return null;
  }
}

/**
 * Cache application data securely in sessionStorage
 * @param {Array|Object} data - Data to cache
 */
export async function saveAppData(data) {
  try {
    const keyResponse = await fetchEncryptionKey();
    if (keyResponse?.success) {
      await Utility.encryptAndStoreArray(
        CONFIG.ENCRYPT_DATA_NAME,
        data,
        keyResponse.ENCRYPTION_KEY,
        { savedAt: new Date().toISOString() }
      );
    }
  } catch (error) {
    console.warn("⚠️ Failed to cache app data:", error);
  }
}

/**
 * Load cached application data if not expired
 * @returns {Promise<Array|Object|null>} Decrypted data or null
 */
export async function loadAppData() {
  try {
    const stored = sessionStorage.getItem(CONFIG.ENCRYPT_DATA_NAME);
    if (!stored) return null;

    const payload = JSON.parse(stored);
    const keyResponse = await fetchEncryptionKey();

    if (keyResponse?.success) {
      const decrypted = await Utility.decryptAndGetArray(
        CONFIG.ENCRYPT_DATA_NAME,
        keyResponse.ENCRYPTION_KEY
      );

      if (!decrypted) return null;

      const now = new Date();
      const savedAt = new Date(payload.savedAt);
      const diffMinutes = (now - savedAt) / (1000 * 60);

      // valid only if same day and < 5 minutes old
      if (now.toDateString() === savedAt.toDateString() && diffMinutes < 5) {
        return decrypted;
      }
      return null; // expired
    }
    return null; // expired or invalid
  } catch (error) {
    console.warn("⚠️ Failed to load app session:", error);
    return null;
  }
}

/**
 * Clear cached application data from sessionStorage
 */
export async function clearAppData() {
  try {
    sessionStorage.removeItem(CONFIG.ENCRYPT_DATA_NAME);
    console.warn("⚠️ App data cleared");
  } catch (error) {
    console.warn("⚠️ Failed to clear app data:", error);
  }
}

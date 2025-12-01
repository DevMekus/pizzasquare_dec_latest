/**
 * Central configuration for the application.
 * Stores API endpoints, token keys, storage keys, and other constants.
 */
export const CONFIG = {
  /** Key name for storing JWT or session token in sessionStorage */
  TOKEN_KEY_NAME: "x-token",

  /** Key name for storing encrypted data in sessionStorage */
  ENCRYPT_DATA_NAME: "x-data",

  /** Base URL for the application (frontend/backend root) */
  BASE_URL: "http://localhost/pizzasq",

  /** API base endpoint */
  API: "http://localhost/pizzasq/api/v1",

  /** Default timeout (ms) for requests or operations */
  TIMEOUT: 1000,
};

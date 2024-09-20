// Function to toggle the login elements based on login status
function toggleLoginElements() {
  const loginItem = document.getElementById('login-item');
  
  // Only proceed if loginItem exists
  if (loginItem) {
    const isLoggedIn = getCookie('isLoggedIn') === 'true';
    loginItem.style.display = isLoggedIn ? "none" : "block";
  }
}

// Function to set a cookie
function setCookie(name, value, days) {
  const expires = new Date();
  expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000));
  document.cookie = `${name}=${value}; expires=${expires.toUTCString()}; path=/`;
}

// Function to get a cookie value
function getCookie(name) {
  const nameEQ = `${name}=`;
  const ca = document.cookie.split(';');
  for (let i = 0; i < ca.length; i++) {
    let c = ca[i].trim();
    if (c.indexOf(nameEQ) === 0) {
      return c.substring(nameEQ.length, c.length);
    }
  }
  return null;
}

// Call the toggleLoginElements function when the DOM is fully loaded
document.addEventListener("DOMContentLoaded", toggleLoginElements);

// Call this function after a successful login
function loginSuccess() {
  setCookie('isLoggedIn', 'true', 1); // Example: Set cookie for 1 day
  toggleLoginElements(); // Update visibility after login
}

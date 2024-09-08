// Function to hide the login elements
function hideLoginElements() {
    document.getElementById('login-item').style.display = "none";
}

// Function to show the login elements
function showLoginElements() {
    document.getElementById('login-item').style.display = "block";
}

// Check if the user is logged in
function checkLoginStatus() {
    const isLoggedIn = getCookie('isLoggedIn');
    if (isLoggedIn === 'true') {
      // User is logged in, hide the login elements
      hideLoginElements();
    } else {
      // User is not logged in, display the login elements
      showLoginElements();
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
      let c = ca[i];
      while (c.charAt(0) === ' ') {
        c = c.substring(1, c.length);
      }
      if (c.indexOf(nameEQ) === 0) {
        return c.substring(nameEQ.length, c.length);
      }
    }
    return null;
  }
  
  
  // Call the checkLoginStatus function when the page loads
  window.onload = function() {
    checkLoginStatus();
};

// Call this function after a successful login
function loginSuccess() {
    setCookie('isLoggedIn', 'true', 1); // Example: Set cookie for 1 day
    checkLoginStatus();
}
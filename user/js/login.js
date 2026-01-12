const container = document.getElementById("container");
const registerBtn = document.getElementById("registerBtn");
const loginBtn = document.getElementById("loginBtn");

/* DEFAULT STATE (LOGIN ACTIVE) */
loginBtn.classList.add("active-btn");

/* REGISTER CLICK */
registerBtn.addEventListener("click", () => {
    container.classList.add("active");

    registerBtn.classList.add("active-btn");
    loginBtn.classList.remove("active-btn");
});

/* LOGIN CLICK */
loginBtn.addEventListener("click", () => {
    container.classList.remove("active");

    loginBtn.classList.add("active-btn");
    registerBtn.classList.remove("active-btn");
});

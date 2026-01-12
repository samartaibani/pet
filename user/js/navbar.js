function toggleProfileMenu(){
    const menu = document.getElementById("profileDropdown");
    menu.style.display = (menu.style.display === "flex") ? "none" : "flex";
}

/* Close when clicking outside */
document.addEventListener("click", function(e){
    const profile = document.querySelector(".profile-box");
    const menu = document.getElementById("profileDropdown");

    if(!profile.contains(e.target)){
        menu.style.display = "none";
    }
});

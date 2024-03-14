var showConcertsButton = document.getElementById("show-concerts");
var pastConcertsDiv = document.getElementById("past-concerts");

showConcertsButton.addEventListener("click", function() {
    if (pastConcertsDiv.classList.contains("d-none")) {
        pastConcertsDiv.classList.remove("d-none");
        showConcertsButton.textContent = "Skrýt odehrané koncerty";
    } else {
        pastConcertsDiv.classList.add("d-none");
        showConcertsButton.textContent = "Odehrané koncerty";
    }
});
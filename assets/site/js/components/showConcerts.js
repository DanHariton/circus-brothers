import AOS from 'aos';
AOS.init();
let showConcertsButton = document.getElementById("show-concerts");
let pastConcertsDiv = document.getElementById("past-concerts");

showConcertsButton.addEventListener("click", function() {
    if (pastConcertsDiv.classList.contains("d-none")) {
        pastConcertsDiv.classList.remove("d-none");
        showConcertsButton.textContent = "Skrýt odehrané koncerty";

        let pastConcertItems = document.querySelectorAll('.past-concert-item');
        let pastConcertItemsArray = Array.from(pastConcertItems);
        AOS.refreshHard(pastConcertItemsArray);
    } else {
        pastConcertsDiv.classList.add("d-none");
        showConcertsButton.textContent = "Odehrané koncerty";
    }
});
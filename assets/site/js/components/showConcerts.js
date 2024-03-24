// import AOS from 'aos';
// AOS.init();
// let showConcertsButton = document.getElementById("show-concerts");
// let pastConcertsDiv = document.getElementById("past-concerts");
//
// showConcertsButton.addEventListener("click", function() {
//     if (pastConcertsDiv.classList.contains("d-none")) {
//         pastConcertsDiv.classList.remove("d-none");
//         showConcertsButton.textContent = "Skrýt odehrané koncerty";
//
//         let pastConcertItems = document.querySelectorAll('.past-concert-item');
//         let pastConcertItemsArray = Array.from(pastConcertItems);
//         AOS.refreshHard(pastConcertItemsArray);
//     } else {
//         pastConcertsDiv.classList.add("d-none");
//         showConcertsButton.textContent = "Odehrané koncerty";
//     }
// });

let showConcertsButton = document.getElementById("show-concerts");
let pastConcertsDiv = document.getElementById("past-concerts");

pastConcertsDiv.classList.add("past-concerts-transition");
showConcertsButton.addEventListener("click", function() {
    if (pastConcertsDiv.classList.contains("past-concerts-visible")) {
        pastConcertsDiv.classList.remove("past-concerts-visible");
        // Применяем класс transition для начала анимации "скрытия"
        pastConcertsDiv.classList.add("past-concerts-transition");
        setTimeout(() => {
            // Опционально: установить display none после завершения анимации, если необходимо
            pastConcertsDiv.style.display = "none";
        }, 500); // Длительность анимации
        showConcertsButton.textContent = "Odehrané koncerty";
    } else {
        pastConcertsDiv.style.display = "block"; // Убедитесь, что элемент видим перед добавлением классов для анимации
        setTimeout(() => {
            pastConcertsDiv.classList.add("past-concerts-visible");
        }, 10); // Небольшая задержка перед добавлением класса для видимости
        showConcertsButton.textContent = "Skrýt odehrané koncerty";
    }
});
document.addEventListener('DOMContentLoaded', () => {

    if(window.location.pathname.includes("index.php")){
        let images = ["images/banner/package1.png", "images/banner/package2.png", "images/banner/package3.png"];
        let currentImage = 0;

        function sliderImage() {
            currentImage++;
            if (currentImage >= images.length) {
                currentImage = 0;
            }
            document.getElementById("slider").src = images[currentImage];
        }

        setInterval(sliderImage, 4000);
}
});
document.addEventListener('DOMContentLoaded', () => {
    if(window.location.pathname.includes("index.php")){
    let colors = ["rgb(249,39,12)", "rgb(252,147,12)", "rgb(253,143,9)", "rgb(155,21,37)"];
    let colorinicial = 0;

    function hotdeals(){
        let deals = document.getElementById("deals");
        deals.style.color = colors[colorinicial];
        colorinicial ++;
        if(colorinicial >= colors.length){
            colorinicial = 0;
        }
    }
    setInterval(hotdeals, 1500);
}
});

    let messageElement = document.getElementById("message");
    if (messageElement) {
        setTimeout(function () {
            messageElement.classList.add("hidden");
        }, 5000);
    }

    
    function validateForm() {
        var countrySelect = document.getElementById("billingCountry");
        var countryError = document.getElementById("countryError");

        if (countrySelect.value === "Selecione o País") {
            countryError.style.display = "block";
            countryError.classList.add("blinking");
            return false;
        } else {
            countryError.style.display = "none";
            countryError.classList.remove("blinking");
            return true;
        }
    }
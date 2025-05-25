//warn user before leaving page if the form is not submitted

document.addEventListener('DOMContentLoaded', function () {
    let form = document.querySelector('form');
    let isFormSubmitted = false;

    form.addEventListener('submit', function () {
        isFormSubmitted = true;
    });

    window.addEventListener('beforeunload', function (e) {
        if (!isFormSubmitted) {
            e.preventDefault();
            e.returnValue = 'Ако напуснете страницата, резултатите няма да бъдат запазени!';
        }
    });
});
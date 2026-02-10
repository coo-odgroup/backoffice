function readCookie(name) {
    let nameEQ = name + "=";
    let ca = document.cookie.split(';');
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i].trim();
        if (c.indexOf(nameEQ) === 0) {
            return c.substring(nameEQ.length);
        }
    }
    return null;
}

function switchClasses(fromClass, toClass) {
    $('.' + fromClass).each(function () {
        $(this).removeClass(fromClass).addClass(toClass);
    });
}

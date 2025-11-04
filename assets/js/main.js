/*=============== SCROLL SECTIONS ACTIVE LINK ===============*/
const sections = document.querySelectorAll('section[id]')

function scrollActive(){
    const scrollY = window.pageYOffset

    sections.forEach(current =>{
        const sectionHeight = current.offsetHeight,
            sectionTop = current.offsetTop - 50,
            sectionId = current.getAttribute('id')

        if(scrollY > sectionTop && scrollY <= sectionTop + sectionHeight){
            document.querySelector('.nav__menu a[href*=' + sectionId + ']').classList.add('active-link')
        }else{
            document.querySelector('.nav__menu a[href*=' + sectionId + ']').classList.remove('active-link')
        }
    })
}
window.addEventListener('scroll', scrollActive)


/*=============== CHANGE BACKGROUND HEADER ===============*/
function scrollHeader(){
    const header = document.getElementById('header')
    // When the scroll is greater than 80 viewport height, add the scroll-header class to the header tag
    if(this.scrollY >= 80) header.classList.add('scroll-header'); else header.classList.remove('scroll-header')
}
window.addEventListener('scroll', scrollHeader)


const forms = document.querySelector(".forms"),
      pwShowHide = document.querySelectorAll(".eye-icon"),
      links = document.querySelectorAll(".link");

pwShowHide.forEach(eyeIcon => {
    eyeIcon.addEventListener("click", () => {
        let pwFields = eyeIcon.parentElement.parentElement.querySelectorAll(".password");
        
        pwFields.forEach(password => {
            if(password.type === "password"){
                password.type = "text";
                eyeIcon.classList.replace("bx-hide", "bx-show");
                return;
            }
            password.type = "password";
            eyeIcon.classList.replace("bx-show", "bx-hide");
        })
        
    })
})      

links.forEach(link => {
    link.addEventListener("click", e => {
       e.preventDefault();
       forms.classList.toggle("show-signup");
    })
})

const pwShowHideAccount = document.querySelectorAll(".eye-icon-account")

pwShowHideAccount.forEach(eyeIcon => {
    eyeIcon.addEventListener("click", () => {
        let pwFields = eyeIcon.parentElement.parentElement.querySelectorAll(".password");
        
        pwFields.forEach(password => {
            if(password.type === "password"){
                password.type = "text";
                eyeIcon.classList.replace("bx-hide", "bx-show");
                return;
            }
            password.type = "password";
            eyeIcon.classList.replace("bx-show", "bx-hide");
        })
        
    })
})  

document.addEventListener("DOMContentLoaded", function () {
    const photoInput = document.querySelector('.upload input[name="photo"]');
    const photoBase64 = document.querySelector('.upload input[name="base64_photo"]');
    const previewImg = document.querySelector('.preview');
    const defaultImgSrc = previewImg.src;

    photoInput.addEventListener('change', function () {
        const file = this.files[0];
        const maxSizeKB = 100;

        if (file) {
            if (file.size > maxSizeKB * 1024) {
                Swal.fire({
                    title: "Error",
                    text: "Ukuran File Tidak Boleh Lebih Dari 100kb",
                    icon: "error",
                    showConfirmButton: false,
                    timer: 2000
                });
                this.value = '';
                photoBase64.value = '';
                previewImg.src = defaultImgSrc;
                return;
            }

            const reader = new FileReader();

            reader.onload = function (e) {
                previewImg.src = e.target.result;
                photoBase64.value = e.target.result;
            };

            reader.readAsDataURL(file);
        }
    });
});

$(document).ready(function() {
    $('.searchable').select2();

    $('.searchdisable').select2({
        minimumResultsForSearch: Infinity
    });
});
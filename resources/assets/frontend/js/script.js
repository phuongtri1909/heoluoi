//hidden password

$(document).on("click", "#togglePassword", function () {
    const passwordField = $("#password");
    const type =
        passwordField.attr("type") === "password" ? "text" : "password";
    passwordField.attr("type", type);

    $(this).toggleClass("fa-eye fa-eye-slash");
});

//hidden password confirm

$(document).on("click", "#togglePasswordConfirm", function () {
    const passwordField = $("#password_confirmation");
    const type =
        passwordField.attr("type") === "password" ? "text" : "password";
    passwordField.attr("type", type);

    $(this).toggleClass("fa-eye fa-eye-slash");
});

//toast
function showToast(message, status) {
    document.addEventListener("DOMContentLoaded", function () {
        const toastElement = document.getElementById("liveToast");
        if (!toastElement) return;

        const toastBody = toastElement.querySelector(".toast-body");
        if (!toastBody) return;

        // Update message
        toastBody.textContent = message;

        // Remove existing classes
        toastElement.classList.remove("bg-success", "bg-danger", "text-white");

        // Add new classes based on status
        if (status === "success") {
            toastElement.classList.add("bg-success", "text-white");
        } else if (status === "error") {
            toastElement.classList.add("bg-danger", "text-white");
        }

        // Initialize and show toast
        const bsToast = new bootstrap.Toast(toastElement);
        bsToast.show();
    });
}

//save toast

function saveToast(message, status) {
    sessionStorage.setItem("toastMessage", message);
    sessionStorage.setItem("toastStatus", status);
}

//show toast
function showSavedToast() {
    const message = sessionStorage.getItem("toastMessage");
    const status = sessionStorage.getItem("toastStatus");

    if (message && status) {
        showToast(message, status);
        sessionStorage.removeItem("toastMessage");
        sessionStorage.removeItem("toastStatus");
    }
}

//otp

function handleInput(element) {
    $(element).val(
        $(element)
            .val()
            .replace(/[^0-9]/g, "")
    );

    if ($(element).val().length === 1) {
        const nextInput = $(element).next(".otp-input");
        if (nextInput.length) {
            nextInput.focus();
        }
    }
}

$(document).on("keydown", ".otp-input", function (e) {
    if (e.key === "Backspace" || e.key === "Delete") {
        e.preventDefault();
        const $currentInput = $(this);
        const $prevInput = $currentInput.prev(".otp-input");

        if ($currentInput.val()) {
            // If current input has value, clear it
            $currentInput.val("");
        } else if ($prevInput.length) {
            // If current input is empty, move to previous input and clear it
            $prevInput.val("").focus();
        }
    }
});

$(document).on("input", ".otp-input", function () {
    const $this = $(this);
    const maxLength = parseInt($this.attr("maxlength"));

    if ($this.val().length > maxLength) {
        $this.val($this.val().slice(0, maxLength));
    }

    // Move to next input if value is entered
    if ($this.val().length === maxLength) {
        const $nextInput = $this.next(".otp-input");
        if ($nextInput.length) {
            $nextInput.focus();
        }
    }
});

// Lưu vị trí cuộn trang khi chuyển trang
$(document).ready(function () {
    $(".click-scroll").on("click", function (e) {
        localStorage.setItem("scrollPosition", $(window).scrollTop());
        window.location.href = $(this).attr("href");
    });

    const scrollPosition = localStorage.getItem("scrollPosition");
    if (scrollPosition) {
        $(window).scrollTop(scrollPosition);
        localStorage.removeItem("scrollPosition");
    }
});
// Lưu vị trí cuộn trang khi chuyển trang

//counter animation

document.addEventListener("DOMContentLoaded", () => {
    const counters = document.querySelectorAll(".counter");
    const speed = 200;

    const startCounting = (element) => {
        const target = parseInt(element.getAttribute("data-target"));
        const count = parseInt(element.innerText);
        const increment = target / speed;

        if (count < target) {
            element.innerText = Math.ceil(count + increment);
            setTimeout(() => startCounting(element), 1);
        } else {
            element.innerText = target;
        }
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                startCounting(entry.target);
                observer.unobserve(entry.target);
            }
        });
    });

    counters.forEach((counter) => observer.observe(counter));
});

// Add this to your JS file that handles the scroll event
document.addEventListener("DOMContentLoaded", function () {
    const navItems = document.querySelectorAll(
        ".transition-header .nav-link, .transition-header .dropdown-toggle"
    );
    navItems.forEach((item, index) => {
        item.style.setProperty("--nav-item-order", index);
    });
});



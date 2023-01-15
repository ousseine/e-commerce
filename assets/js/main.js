import '../styles/main.css'

// Example starter JavaScript for disabling form submissions if there are invalid fields
(() => {
    'use strict'

    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    const forms = document.querySelectorAll('.needs-validation')

    // Loop over them and prevent submission
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }

            form.classList.add('was-validated')
        }, false)
    })
})()


// visible password
document.querySelectorAll('.icon-eye').forEach(icon => {
    icon.addEventListener('click', function (e) {
        e.preventDefault()

        const inputs = document.querySelectorAll('.password')

        inputs.forEach(input => {
            if(input.type === 'password') {
                input.type = 'text'
                this.classList.remove('bi-eye-slash')
                this.classList.add('bi-eye')
            }
            else {
                input.type = 'password'
                this.classList.add('bi-eye-slash')
                this.classList.remove('bi-eye')
            }
        })


        console.log('test ok')
    })
})
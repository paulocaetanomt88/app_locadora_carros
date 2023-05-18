window._ = require('lodash');

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

try {
    window.Popper = require('popper.js').default;
    window.$ = window.jQuery = require('jquery');

    require('bootstrap');
} catch (e) {}

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     forceTLS: true
// });

/*
    !!! INTERCEPTAR OS REQUESTS DA APLICAÇÃO !!!
    O método use espera 2 métodos de callback:
        - O 1º método que vai definir as configurações da requisição antes que ela aconteça
        - O 2º vai recuperar por parâmetro o erro caso aconteça, podendo retornar a Promise.reject do erro
*/
axios.interceptors.request.use(
    config => {
        console.log('Interceptando o request antes do envio', config)
        return config
    },
    error => {
        console.log('Erro na requisição: ', error)
        return Promise.reject(error)
    }
)

//   INTERCEPTAR OS RESPONSES DA APLICAÇÃO
// Se uma requisição for feita com sucesso, esta vai retornar uma resposta
// mas antes que esta resposta for absorvida por uma aplicação nós poderemos interceptar e tratar isso
// aplicando lógicas que serão comuns pra todas essas respostas
axios.interceptors.response.use(
    // o primeiro método é para tratar a resposta recebida dessa requisição
    response => {
        console.log('Interceptando a resposta antes da aplicação', response)
        return response
    },
    // tratar os erros caso aconteçam
    error => {
        console.log('Erro na resposta: ', error)
        return Promise.reject(error)
    }
)

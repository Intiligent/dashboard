window.addEventListener('error', function(message, url, line, column, error) {
    console.log('Error:', url, line, column, message);
    console.log(error);
});

window.addEventListener('unhandledrejection', function(event) {
    console.log('Unhandled rejection:', event);
});

export default {
    install(app) {
        app.config.unwrapInjectedRef = true;
    },
}

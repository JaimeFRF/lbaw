function getContextualHelp(field, helpId) {
    const contextualField = document.querySelector(`input[name="${field}"]`);
    const contextualHelp = document.getElementById(helpId);

    return {
        show: function() {
            contextualHelp.style.display = 'block';
        },
        hide: function() {
            contextualHelp.style.display = 'none';
        }
    };
}
(function($, undefined) {
    function confirmDeletion (event) {
        var needConfirmation = !event.target.hasAttribute('data-no-confirm');

        if (needConfirmation && !confirm($(event.target).data('confirm') || 'Are you sure?')) {
            return false;
        }

        return true;
    }

    $(document).ready(function() {
        $('body').delegate('a[data-method]', 'click', function (event) {
            event.preventDefault();
            var method = $(event.target).data('method');

            if (method.toLowerCase() === 'delete' && !confirmDeletion(event)) {
                return;
            }

            var form      = document.createElement('form');
            var input     = document.createElement('input');
            var csrfInput = document.createElement('input');
            var csrfToken = $(event.target).data('csrf-token');

            form.method   = 'POST';
            form.action   = event.currentTarget.href;

            input.type    = 'hidden';
            input.name    = '_method';
            input.value   = method;

            form.appendChild(input);

            if (csrfToken) {
                csrfInput.type  = 'hidden';
                csrfInput.name  = '_link_token';
                csrfInput.value = csrfToken;
                form.appendChild(csrfInput);
            }

            document.body.appendChild(form);

            form.submit();
        });
    });
})( jQuery );

jQuery(document).ready(function($) {
    // Use delegation to handle potential dynamic additions (though unlikely in standard table)
    $(document).on('click', '.ec-copy-shortcode', function(e) {
        e.preventDefault();
        var btn = $(this);
        var text = btn.data('clipboard-text');
        var input = btn.prev('input');
        
        // Try fallback first if navigator.clipboard is missing/untrusted (common in local dev without HTTPS)
        // Or just try navigator, then catch.
        if (navigator.clipboard) {
             navigator.clipboard.writeText(text).then(function() {
                showSuccess(btn);
            }).catch(function(err) {
                console.warn('Clipboard API failed: ', err);
                fallbackCopy(input, btn);
            });
        } else {
            fallbackCopy(input, btn);
        }
    });

    function fallbackCopy(inputRef, btnRef) {
        try {
            // Ensure we have the DOM element
            if (!inputRef || inputRef.length === 0) return;
            
            var nativeInput = inputRef[0];
            nativeInput.focus();
            nativeInput.select();
            
            // For mobile compatibility
            nativeInput.setSelectionRange(0, 99999); 

            var successful = document.execCommand('copy');
            if (successful) {
                showSuccess(btnRef);
            } else {
                 console.error('Fallback copy failed.');
            }
        } catch (err) {
            console.error('Fallback copy error: ', err);
        }
    }

    function showSuccess(btn) {
        var OriginalText = btn.html();
        var msg = btn.next('.ec-copy-success');
        
        btn.css('border-color', 'green').css('color', 'green');
        btn.find('.dashicons').css('color', 'green');
        
        msg.fadeIn('fast').delay(1500).fadeOut('fast', function() {
             btn.removeAttr('style');
             btn.find('.dashicons').removeAttr('style');
        });
    }
});

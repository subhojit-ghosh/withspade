<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<script>
    function showToast(text, type) {
        let color = '#1FAA59';
        if (type == 'error') color = '#FF6263';
        Toastify({
            text,
            close: true,
            gravity: "top",
            position: "right",
            style: {
                background: color
            }
        }).showToast();
    }
</script>

<footer class="mt-4 py-3 border-top text-center" style="background-color: var(--neutral-100); color: var(--text);">
    <div class="container">
        <small>&copy; <span id="year"></span> <?= BRAND_NAME ?>. All rights reserved.</small>
        <br>
        <small>Version 1.0 | <a href="#" class="text-decoration-none" style="color: var(--primary);">Support</a> | <a href="#" class="text-decoration-none" style="color: var(--primary);">Privacy Policy</a></small>
    </div>
</footer>
<script>
    document.getElementById("year").textContent = new Date().getFullYear();
</script>
<nav class="navbar container-fluid navbar-light bg-white position-sticky top-0">
    <div class=""><i class="fal fa-caret-circle-down h5 d-none d-md-block menutoggle fa-rotate-90"></i>
        <i class="fas fa-bars h4  d-md-none"></i>
    </div>
    <div class="d-flex align-items-center gap-4">
        <form class="d-flex align-items-center" method="GET" action="course.php">
            <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search" id="searchInput" name="searchInput">
        </form>
    </div>
</nav>

<script>
document.getElementById('searchInput').addEventListener('input', function() {
    const searchInput = this.value;
    fetch(`course.php?searchInput=${searchInput}`)
        .then(response => response.text())
        .then(data => {
            document.getElementById('coursesTable').innerHTML = data;
        });
});
</script>
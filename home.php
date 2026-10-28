<?php 
include 'admin/db_connect.php'; 
?>
<link rel="stylesheet" href="styles.css">
<style>

.vacancy-list {
    cursor: pointer;
    margin-top: 20px;
    width: 100%;
    background-color: #fff; 
    border: 1px solid #ddd; 
    border-radius: 8px; 
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3); 
    overflow: hidden; 
}

span.highlight {
    background: yellow;
}

.search-container {
    text-align: center;
    margin-top: 90px;
}

#searchBar {
    width: 50%;
    padding: 10px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 5px;
}
</style>

<div class="container pt-2">

    
<!-- Search Bar -->
    <div class="search-container">
        <input type="text" id="searchBar" placeholder="Search for vacancies" onkeyup="filterVacancies()">
    </div>

    <?php
    $vacancy = $conn->query("SELECT * FROM vacancy ORDER BY date(date_created) DESC");
    while ($row = $vacancy->fetch_assoc()):
        $trans = get_html_translation_table(HTML_ENTITIES, ENT_QUOTES);
        unset($trans["\""], $trans["<"], $trans[">"], $trans["<h2"]);
        $desc = strtr(html_entity_decode($row['description']), $trans);
        $desc = str_replace(array("<li>", "</li>"), array("", ","), $desc);
    ?>
    <div class="card vacancy-list" data-id="<?php echo $row['id'] ?>">
        <div class="card-body">
            <h3><b class="filter-txt"><?php echo $row['position'] ?></b></h3>
            <span><small>Needed: <?php echo $row['availability'] ?></small></span>
            <hr>
            <span class="truncate filter-txt"><?php echo strip_tags($desc) ?></span>
        </div>
    </div>
    <br>
    <?php endwhile; ?>
</div>

<script>
    // Vacancy List Click Event
    document.querySelectorAll('.card.vacancy-list').forEach(function(card) {
        card.addEventListener('click', function() {
            var id = card.getAttribute('data-id');
            window.location.href = "index.php?page=view_vacancy&id=" + id;
        });
    });

    // Filter Functionality
    function filterVacancies() {
        var filter = document.getElementById('searchBar').value.toLowerCase();
        document.querySelectorAll('.card.vacancy-list .filter-txt').forEach(function(txtElement) {
            var txt = txtElement.textContent.toLowerCase();
            var card = txtElement.closest('.card');
            if (txt.includes(filter)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }
</script>

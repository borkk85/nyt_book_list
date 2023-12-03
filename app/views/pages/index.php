<?php require dirname(__DIR__) . '/inc/header.php'; ?>

<h1>Books</h1>

<?php if (!empty($data)): ?>
    <ul>
        <?php foreach ($data as $book): ?>
            <li>
                <h2><?php echo htmlspecialchars($book['title']); ?></h2>
                <p>Author: <?php echo htmlspecialchars($book['author']); ?></p>
                <p>ISBN: <?php echo htmlspecialchars($book['isbn13']); ?></p>
                <p>Genres: <?php echo htmlspecialchars($book['genres']); ?></p>
                <!-- Add more fields as needed -->
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>No books found.</p>
<?php endif; ?>

<?php require dirname(__DIR__) . '/inc/footer.php'; ?>



<?php require dirname(__DIR__) . '/inc/header.php'; ?>

<main>
    <div class="hero-section">
        <div class="hero-overlay">
            <h2></h2>
            <p></p>
        </div>
    </div>
    <div class="book-section">
        <nav class="category-nav">
            <ul>
                <li><a href="/nyt_book_list/";>All Books</a></li>
                <?php foreach ($data['categories'] as $category) : ?>
                    <li><a href="/nyt_book_list/?category=<?php echo urlencode($category); ?>"><?php echo htmlspecialchars($category); ?></a></li>
                <?php endforeach; ?>
            </ul>
        </nav>

        <?php if (!empty($data)) : ?>
            <div class="book-listings">
                <?php foreach ($data['books'] as $book) : ?>
                    <!-- <?php echo '<pre>', var_dump($book), '</pre>'; ?> -->
                    <div class="book">
                        <img src="<?php echo $book['book_image_url']; ?>" alt="Book Cover">
                        <div class="book-details">
                            <h3><?php echo htmlspecialchars($book['title']); ?></h3>
                            <p><?php echo htmlspecialchars($book['author']); ?></p>
                            <p>New York Times Bestseller</p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        <?php else : ?>
            <p>No books found.</p>
        <?php endif; ?>

    </div>

</main>
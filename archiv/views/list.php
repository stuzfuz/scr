<?php 

use Data\DataManager;

$categories = DataManager::getCategories(); 
$categoryId = isset($_REQUEST['categoryId']) ? (int)$_REQUEST['categoryId'] : null; 

$books = (isset($categoryId)) && ($categoryId > 0) ? DataManager::getBooksByCategory($categoryId) : null; 

require_once('views/partials/header.php') ?>

<div class="page_header">
    <h2>List of Book categories</h2>
</div>

<ul class="nav nav-tabs">
    <?php foreach($categories as $cat) :    ?>
        <li class="item <?php if ($cat->getId() === $categoryId) { ?> active <?php } ?> ">
            <a 
            href="<?php echo $_SERVER['PHP_SELF']   ?>?view=list&categoryId=<?php echo urlencode($cat->getId()) ?> ">
                <?php echo $cat->getName()  ?>  
            </a>
        </li>


    <?php endforeach; ?>
</ul>

<?php if (isset($books)): ?>     
    <?php if (sizeof($books) > 0) : 
        require('views/partials/booklist.php');
          else: ?>
        <div class="alert alert-warning" role="alert">No book ins this cateogry</div>
    <?php endif; ?>
<?php else: ?>
    <br/>
    <div class="alert alert-info" role="alert">Please select a category</div>
<?php endif; ?>



<?php require_once('views/partials/footer.php') ?>
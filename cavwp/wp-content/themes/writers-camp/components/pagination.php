<?php

use cavWP\Utils;

$nav_items = Utils::paginate_links(
   [
      'mid_size'  => 3,
      'next_text' => 'Próxima página <i class="ri-arrow-right-line"></i>',
      'prev_text' => '<i class="ri-arrow-left-line"></i> Página anterior',
   ],
   [
      'page-numbers current' => 'btn active',
      'page-numbers'         => 'btn',
   ],
);

if (!empty($nav_items)) {
   ?>
<nav class="mt-12">
   <ul class="flex justify-center gap-2 *:flex">
      <?php foreach ($nav_items as $nav_item) { ?>
      <li>
         <?php echo $nav_item; ?>
      </li>
      <?php } ?>
   </ul>
</nav>
<?php

}

?>

<?php

use cavWP\Models\Post;

get_component('header');

$Text = new Post();

?>
<main class="main">
   <div class="flex flex-col justify-start gap-9">
      <hgroup class="w-full max-w-200">
         <h1 class="h1">
            <?php echo $Text->get('title'); ?>
         </h1>
         <div
            class="text-2xl font-medium w-full py-2">
            <?php echo $Text->get('summary'); ?>
         </div>
      </hgroup>
      <div class="content !px-0">
         <?php echo $Text->get('content'); ?>
      </div>
   </div>
</main>
<?php

get_component('footer');

?>

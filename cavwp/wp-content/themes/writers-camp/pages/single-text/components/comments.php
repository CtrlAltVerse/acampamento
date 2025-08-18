<?php

use writersCampP\Text\Walker_Comment;

$comments = wp_list_comments(
   [
      'type'              => 'comment',
      'reverse_top_level' => true,
      'page'              => 1,
      'walker'            => new Walker_Comment(),
      'echo'              => false,
   ],
);

?>
<ul id="comments-list" class="flex flex-col gap-10">
   <?php echo $comments; ?>
</ul>

<?php

namespace writersCampP\Text;

class Walker_Comment extends \Walker_Comment
{
   protected function html5_comment($comment, $depth, $args): void
   {
      $tag = ('div' === $args['style']) ? 'div' : 'li';

      $commenter          = wp_get_current_commenter();
      $show_pending_links = !empty($commenter['comment_author']);

      if ($commenter['comment_author_email']) {
         $moderation_note = __('Your comment is awaiting moderation.');
      } else {
         $moderation_note = __('Your comment is awaiting moderation. This is a preview; your comment will be visible after it has been approved.');
      }

      ?>
<<?php echo $tag; ?>
   id="comment-<?php comment_ID(); ?>"
   <?php comment_class($this->has_children ? 'parent' : '', $comment); ?>>
   <article class="flex flex-col gap-3">
      <footer class="flex items-center gap-3">
         <div class="comment-author flex gap-2.5 items-center rounded-l rounded-r-full font-medium">
            <?php if (0 !== $args['avatar_size']) {
               echo get_avatar($comment, $args['avatar_size'], args: ['class' => 'rounded']);
            } ?>
            <?php

            $comment_author = get_comment_author_link($comment);

      if ('0' === $comment->comment_approved && !$show_pending_links) {
         $comment_author = get_comment_author($comment);
      }

      echo $comment_author;

      ?>
         </div>
         <?php printf(
            '<a class="text-sm text-neutral-500 dark:text-neutral-300" href="%s"><time datetime="%s">%s</time></a>',
            esc_url(get_comment_link($comment, $args)),
            get_comment_time('c'),
            get_comment_date('', $comment),
         ); ?>
         <?php if ('0' === $comment->comment_approved) { ?>
         <em class="comment-awaiting-moderation">
            <?php echo $moderation_note; ?>
         </em>
         <?php } ?>
      </footer>
      <div>
         <?php comment_text(); ?>
      </div>
      <div class="flex items-center gap-3 text-neutral-500 dark:text-neutral-300">
         <?php if (is_user_logged_in() && '1' === $comment->comment_approved || $show_pending_links) {
            comment_reply_link(
               array_merge(
                  $args,
                  [
                     'add_below'     => 'div-comment',
                     'depth'         => $depth,
                     'max_depth'     => $args['max_depth'],
                     'before'        => '<div class="reply">',
                     'after'         => '</div>',
                     'reply_to_text' => 'Respondendo %s',
                  ],
               ),
               $comment,
            );
         } ?>
         <?php edit_comment_link(__('Edit'), ' <span class="edit-link">', '</span>'); ?>
      </div>
   </article>
   <?php
   }
}
?>

<?php

use cavWP\Models\User;
use writersCampP\Utils;

$writer = new User(is_author() ? null : get_current_user_id());
$rank   = !empty($args['rank']) ? $args['rank'] : Utils::get_rank($writer->get('xp', default: '0'));
$avatar = $args['avatar'] ?? false;

?>
<div class="ribbon <?php echo $rank->color; ?>">
   <span class="title"><?php echo $rank->label; ?></span>
   <?php if ($avatar) { ?>
   <div x-show="!<?php echo $avatar; ?>">
      <?php } ?>
      <?php echo $writer->get('avatar', attrs: [
         'class' => 'rounded-full object-cover border-2 border-middle',
      ]); ?>
      <?php if ($avatar) { ?>
   </div>
   <img class="rounded-full object-cover size-24 border-2 border-middle"
        x-show="<?php echo $avatar; ?>"
        x-bind:src="<?php echo $avatar; ?> && URL.createObjectURL(<?php echo $avatar; ?>)" x-cloak />
   <?php } ?>
   <span class="grow flex items-center justify-center text-3xl font-semibold">
      <?php echo $rank->level; ?>
   </span>
</div>
